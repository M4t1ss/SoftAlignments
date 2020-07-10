# coding: utf-8

import io, unicodedata, re, sys, getopt, string, os, webbrowser, math, ntpath, numpy as np
import tempfile, shutil
from time import gmtime, strftime
from imp import reload
from softalignments.functions import *
try:
    import configparser as cp
except ImportError:
    import ConfigParser as cp

def main():
    argv = sys.argv[1:]
    try:
        opts, args = getopt.getopt(argv,"hi:o:s:t:f:n:a:b:c:d:g:r:v:w:x:y:")
    except getopt.GetoptError:
        printHelp()
        sys.exit(2)
    for opt, arg in opts:
        if opt == '-h':
            printHelp()
            sys.exit()
        elif opt == '-i':
            inputfile = arg
        elif opt == '-o':
            outputType = arg
        elif opt == '-s':
            sourcefile = arg
        elif opt == '-t':
            targetfile = arg
        elif opt == '-f':
            from_system = arg
        elif opt == '-n':
            num = arg
        elif opt == '-v':
            from_system2 = arg
        elif opt == '-w':
            inputfile2 = arg
        elif opt == '-x':
            sourcefile2 = arg
        elif opt == '-y':
            targetfile2 = arg
        elif opt == '-c':
            config_file = arg
        elif opt == '-r':
            referencefile = arg
        elif opt == '-d':
            de_bpe = True
    
    try:
        config_file
    except NameError:
        config_file = False
    try:
        de_bpe
    except NameError:
        de_bpe = False
    
    if(config_file):
        # There is a config file! Get info about inputs
        config = cp.ConfigParser()
        config.read(config_file)
        try:
            referencefile = config.get('AlignmentsOne', 'ReferenceFile')
        except (cp.NoOptionError, cp.NoSectionError):
            referencefile = False
        try:
            inputfile = config.get('AlignmentsOne', 'InputFile')
        except NameError:
            print ('Provide an input file!\n')
            printHelp()
            sys.exit()
        try:
            from_system = config.get('AlignmentsOne', 'From')
        except cp.NoOptionError:
            from_system = 'NeuralMonkey'
        try:
            num = config.getint('Options', 'Number')
        except (cp.NoOptionError, cp.NoSectionError):
            num = -1
        try:
            outputType = config.get('Options', 'OutputType')
        except (cp.NoOptionError, cp.NoSectionError):
            # Set output type to 'web' by default
            outputType = 'web'
        
        if from_system == 'NeuralMonkey' or from_system == 'Marian':
            try:
                sourcefile = config.get('AlignmentsOne', 'SourceFile')
            except cp.NoOptionError:
                print ('Provide a source sentence file!\n')
                printHelp()
                sys.exit()
            if from_system == 'NeuralMonkey':
                try:
                    targetfile = config.get('AlignmentsOne', 'TargetFile')
                except cp.NoOptionError:
                    print ('Provide a target sentence file!\n')
                    printHelp()
                    sys.exit()
        if outputType == 'compare':
            try:
                from_system2 = config.get('AlignmentsTwo', 'From')
            except cp.NoOptionError:
                from_system2 = 'NeuralMonkey'
            try:
                inputfile2 = config.get('AlignmentsTwo', 'InputFile')
            except cp.NoOptionError:
                print ('Provide a input file for the second system!\n')
                printHelp()
                sys.exit()
            if from_system2 == 'NeuralMonkey' or from_system2 == 'Marian':
                try:
                    sourcefile2 = config.get('AlignmentsTwo', 'SourceFile')
                except cp.NoOptionError:
                    print ('Provide a source sentence file for the second system!\n')
                    printHelp()
                    sys.exit()
                if from_system2 == 'NeuralMonkey':
                    try:
                        targetfile2 = config.get('AlignmentsTwo', 'TargetFile')
                    except cp.NoOptionError:
                        print ('Provide a target sentence file for the second system!\n')
                        printHelp()
                        sys.exit()
        
    else:
        # There is no config file. Look for inputs in parameters
        try:
            referencefile
        except NameError:
            referencefile = False
        try:
            inputfile
        except NameError:
            print ('Provide an input file!\n')
            printHelp()
            sys.exit()
        try:
            from_system
        except NameError:
            from_system = 'NeuralMonkey'
        try:
            num
        except NameError:
            num = -1
        try:
            outputType
        except NameError:
            # Set output type to 'web' by default
            outputType = 'web'
        if from_system == 'NeuralMonkey' or from_system == 'Marian':
            try:
                sourcefile
            except NameError:
                print ('Provide a source sentence file!\n')
                printHelp()
                sys.exit()
            if from_system == 'NeuralMonkey':
                try:
                    targetfile
                except NameError:
                    print ('Provide a target sentence file!\n')
                    printHelp()
                    sys.exit()
        if outputType == 'compare':
            try:
                from_system2
            except NameError:
                from_system2 = 'NeuralMonkey'
            try:
                inputfile2
            except NameError:
                print ('Provide a input file for the second system!\n')
                printHelp()
                sys.exit()
            if from_system2 == 'NeuralMonkey' or from_system2 == 'Marian':
                try:
                    sourcefile2
                except NameError:
                    print ('Provide a source sentence file for the second system!\n')
                    printHelp()
                    sys.exit()
                if from_system2 == 'NeuralMonkey':
                    try:
                        targetfile2
                    except NameError:
                        print ('Provide a target sentence file for the second system!\n')
                        printHelp()
                        sys.exit()
    
    if outputType != 'color' and outputType != 'block' and outputType != 'block2' and outputType != 'compare':
        # Set output type to 'web' by default
        outputType = 'web'

    if from_system == "NeuralMonkey":
        srcs = readSnts(sourcefile)
        tgts = readSnts(targetfile)
        alis = np.load(inputfile)
    if from_system == "Nematus" or from_system == "Sockeye" or from_system == "OpenNMT" or from_system == "Marian-Dev":
        (srcs, tgts, alis) = readNematus(inputfile, from_system, de_bpe)
    if from_system == "Marian":
        (srcs, tgts, alis) = readAmu(inputfile, sourcefile)

    data = list(zip(srcs, tgts, alis))
    
    if outputType == 'compare':
        if from_system2 == "NeuralMonkey":
            srcs2 = readSnts(sourcefile2)
            tgts2 = readSnts(targetfile2)
            alis2 = np.load(inputfile2)
        if from_system2 == "Nematus" or from_system2 == "Sockeye":
            (srcs2, tgts2, alis2) = readNematus(inputfile2, from_system2, de_bpe)
        if from_system2 == "OpenNMT":
            (srcs2, tgts2, alis2) = readNematus(inputfile2, from_system2, de_bpe)
        if from_system2 == "Marian":
            (srcs2, tgts2, alis2) = readAmu(inputfile2, sourcefile2)
        data2 = list(zip(srcs2, tgts2, alis2))
        
        if compare(srcs, srcs2) == False:
            print ('Source sentences from both systems need to be identical!\n')
            printHelp()
            sys.exit()

    foldername = ntpath.basename(inputfile).replace(".","") + "_" + strftime("%d%m_%H%M", gmtime())
    if outputType == 'compare':
        foldername = 'cmp_' + foldername
    if outputType == 'web' or outputType == 'compare':
        folder = './web/data/' + foldername
    else:
        folder = tempfile.mkdtemp()

    try:
        os.stat(folder)
    except:
        os.mkdir(folder)
        
    if(referencefile):
        shutil.copyfile(referencefile, folder + "/" + ntpath.basename(inputfile) + '.ref.txt')
        refs = readSnts(referencefile)
    else:
        refs = False
    
    if outputType == 'compare':
        try:
            os.stat(folder + '/NMT1')
        except:
            os.mkdir(folder + '/NMT1')
        try:
            os.stat(folder + '/NMT2')
        except:
            os.mkdir(folder + '/NMT2')
        synchData(data,data2)
        processAlignments(data, folder + '/NMT1', inputfile, outputType, num, refs)
        processAlignments(data2, folder + '/NMT2', inputfile2, outputType, num, refs)
    else:
        processAlignments(data, folder, inputfile, outputType, num, refs)
            
    # Get rid of some junk
    if outputType == 'web' or outputType == 'compare':
        webbrowser.open("http://127.0.0.1:47155/?directory=" + foldername)
        os.system("php -S 127.0.0.1:47155 -t web")
    else:
        os.remove(folder + "/" + ntpath.basename(inputfile) + '.ali.js')
        os.remove(folder + "/" + ntpath.basename(inputfile) + '.src.js')
        os.remove(folder + "/" + ntpath.basename(inputfile) + '.trg.js')
        os.remove(folder + "/" + ntpath.basename(inputfile) + '.con.js')
        os.remove(folder + "/" + ntpath.basename(inputfile) + '.sc.js')
        shutil.rmtree(folder)

if __name__ == "__main__":
    if sys.version[0] == '2':
        reload(sys)
        sys.setdefaultencoding('utf-8')
    main()
