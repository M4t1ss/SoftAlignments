# coding: utf-8

import unicodedata, re, functions, sys, getopt, string, os, webbrowser, math, ntpath, numpy as np
from time import gmtime, strftime
from io import open, StringIO
from imp import reload

def main(argv):
    try:
        opts, args = getopt.getopt(argv,"hi:o:s:t:f:n:a:b:c:d:")
    except getopt.GetoptError:
        functions.printHelp()
        sys.exit(2)
    for opt, arg in opts:
        if opt == '-h':
            functions.printHelp()
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
        elif opt == '-a':
            inputfile2 = arg
        elif opt == '-b':
            sourcefile2 = arg
        elif opt == '-c':
            targetfile2 = arg
        elif opt == '-d':
            from_system2 = arg
    try:
        inputfile
    except NameError:
        print ('Provide an input file!')
        functions.printHelp()
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
            print ('Provide a source sentence file!')
            functions.printHelp()
            sys.exit()
        if from_system == 'NeuralMonkey':
            try:
                targetfile
            except NameError:
                print ('Provide a target sentence file!')
                functions.printHelp()
                sys.exit()
    if outputType != 'color' and outputType != 'block' and outputType != 'block2' and outputType != 'compare':
        # Set output type to 'web' by default
        outputType = 'web'

    if from_system == "NeuralMonkey":
        srcs = functions.readSnts(sourcefile)
        tgts = functions.readSnts(targetfile)
        alis = np.load(inputfile)
    if from_system == "Nematus" or from_system == "Sockeye":
        (srcs, tgts, alis) = functions.readNematus(inputfile)
    if from_system == "OpenNMT":
        (srcs, tgts, alis) = functions.readNematus(inputfile, 1)
    if from_system == "Marian":
        (srcs, tgts, alis) = functions.readAmu(inputfile, sourcefile)

    data = list(zip(srcs, tgts, alis))
    
    if outputType == 'compare':
        if from_system2 == "NeuralMonkey":
            srcs2 = functions.readSnts(sourcefile2)
            tgts2 = functions.readSnts(targetfile2)
            alis2 = np.load(inputfile2)
        if from_system2 == "Nematus" or from_system2 == "Sockeye":
            (srcs2, tgts2, alis2) = functions.readNematus(inputfile2)
        if from_system2 == "OpenNMT":
            (srcs2, tgts2, alis2) = functions.readNematus(inputfile2, 1)
        if from_system2 == "Marian":
            (srcs2, tgts2, alis2) = functions.readAmu(inputfile2, sourcefile2)
        data2 = list(zip(srcs2, tgts2, alis2))

    foldername = ntpath.basename(inputfile).replace(".","") + "_" + strftime("%d%m_%H%M", gmtime())
    folder = './web/data/' + foldername
    try:
        os.stat(folder)
    except:
        os.mkdir(folder)
    
    if outputType == 'compare':
        try:
            os.stat(folder + '/NMT1')
        except:
            os.mkdir(folder + '/NMT1')
        try:
            os.stat(folder + '/NMT2')
        except:
            os.mkdir(folder + '/NMT2')
        functions.processAlignments(data, folder + '/NMT1', inputfile, outputType, num)
        functions.processAlignments(data2, folder + '/NMT2', inputfile2, outputType, num)
    else:
        functions.processAlignments(data, folder, inputfile, outputType)
            
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
        os.rmdir(folder)

if __name__ == "__main__":
    if sys.version[0] == '2':
        reload(sys)
        sys.setdefaultencoding('utf-8')
    main(sys.argv[1:])
