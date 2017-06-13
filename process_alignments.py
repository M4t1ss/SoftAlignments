# coding: utf-8

import thecode
import sys, getopt
import numpy as np
import string
import os
import webbrowser
from time import gmtime, strftime
import ntpath
from io import open
from io import StringIO

def printColor(value):
    if float(value) == 0:
        sys.stdout.write('[48;5;232m[K  [m[K' )
    elif float(value) > 0.00 and float(value) <= 0.04:
        sys.stdout.write('[48;5;233m[K  [m[K' )
    elif float(value) > 0.04 and float(value) <= 0.08:
        sys.stdout.write('[48;5;234m[K  [m[K' )
    elif float(value) > 0.08 and float(value) <= 0.13:
        sys.stdout.write('[48;5;235m[K  [m[K' )
    elif float(value) > 0.13 and float(value) <= 0.17:
        sys.stdout.write('[48;5;236m[K  [m[K' )
    elif float(value) > 0.17 and float(value) <= 0.21:
        sys.stdout.write('[48;5;237m[K  [m[K' )
    elif float(value) > 0.21 and float(value) <= 0.25:
        sys.stdout.write('[48;5;238m[K  [m[K' )
    elif float(value) > 0.25 and float(value) <= 0.29:
        sys.stdout.write('[48;5;239m[K  [m[K' )
    elif float(value) > 0.29 and float(value) <= 0.33:
        sys.stdout.write('[48;5;240m[K  [m[K' )
    elif float(value) > 0.33 and float(value) <= 0.38:
        sys.stdout.write('[48;5;241m[K  [m[K' )
    elif float(value) > 0.38 and float(value) <= 0.42:
        sys.stdout.write('[48;5;242m[K  [m[K' )
    elif float(value) > 0.42 and float(value) <= 0.46:
        sys.stdout.write('[48;5;243m[K  [m[K' )
    elif float(value) > 0.46 and float(value) <= 0.50:
        sys.stdout.write('[48;5;244m[K  [m[K' )
    elif float(value) > 0.50 and float(value) <= 0.54:
        sys.stdout.write('[48;5;245m[K  [m[K' )
    elif float(value) > 0.54 and float(value) <= 0.58:
        sys.stdout.write('[48;5;246m[K  [m[K' )
    elif float(value) > 0.58 and float(value) <= 0.63:
        sys.stdout.write('[48;5;247m[K  [m[K' )
    elif float(value) > 0.63 and float(value) <= 0.67:
        sys.stdout.write('[48;5;248m[K  [m[K' )
    elif float(value) > 0.67 and float(value) <= 0.71:
        sys.stdout.write('[48;5;249m[K  [m[K' )
    elif float(value) > 0.71 and float(value) <= 0.75:
        sys.stdout.write('[48;5;250m[K  [m[K' )
    elif float(value) > 0.75 and float(value) <= 0.79:
        sys.stdout.write('[48;5;251m[K  [m[K' )
    elif float(value) > 0.79 and float(value) <= 0.83:
        sys.stdout.write('[48;5;252m[K  [m[K' )
    elif float(value) > 0.83 and float(value) <= 0.88:
        sys.stdout.write('[48;5;253m[K  [m[K' )
    elif float(value) > 0.88 and float(value) <= 0.92:
        sys.stdout.write('[48;5;254m[K  [m[K' )
    elif float(value) > 0.92 and float(value) <= 0.96:
        sys.stdout.write('[48;5;255m[K  [m[K' )
    elif float(value) > 0.96 and float(value) <= 1:
        sys.stdout.write('[48;5;255m[K  [m[K' )
    else:
        sys.stdout.write('[48;5;232m[K  [m[K' )

def printBlock2(value):
    if float(value) == 0:
        sys.stdout.write('██')
    elif float(value) > 0 and float(value) <= 0.125:
        sys.stdout.write('▉▉')
    elif float(value) > 0.125 and float(value) <= 0.250:
        sys.stdout.write('▊▊')
    elif float(value) > 0.250 and float(value) <= 0.375:
        sys.stdout.write('▋▋')
    elif float(value) > 0.375 and float(value) <= 0.500:
        sys.stdout.write('▌▌')
    elif float(value) > 0.500 and float(value) <= 0.625:
        sys.stdout.write('▍▍')
    elif float(value) > 0.625 and float(value) <= 0.750:
        sys.stdout.write('▎▎')
    elif float(value) > 0.750 and float(value) <= 0.875:
        sys.stdout.write('▏▏')
    elif float(value) > 0.875 and float(value) <= 1:
        sys.stdout.write('  ')
    else:
        sys.stdout.write('██')
        
def printBlock(value):
    if float(value) == 0:
        sys.stdout.write('██')
    elif float(value) > 0 and float(value) <= 0.25:
        sys.stdout.write('▓▓')
    elif float(value) > 0.25 and float(value) <= 0.5:
        sys.stdout.write('▒▒')
    elif float(value) > 0.5 and float(value) <= 0.75:
        sys.stdout.write('░░')
    elif float(value) > 0.75 and float(value) <= 1:
        sys.stdout.write('  ')
    else:
        sys.stdout.write('██')
        
def readSnts(filename):
    with open(filename, 'r', encoding='utf-8') as fh:
        return [line.strip().split() for line in fh]

def readNematus(filename):
    with open(filename, 'r', encoding='utf-8') as fh:
        alis = []
        tgts = []
        srcs = []
        wasNew = True
        aliTXT = ''
        for line in fh:
            if wasNew:
                if len(aliTXT) > 0:
                    c = StringIO(aliTXT)
                    ali = np.loadtxt(c)
                    ali = ali.transpose()
                    alis.append(ali)
                    aliTXT = ''
                lineparts = line.split(' ||| ')
                lineparts[1] += ' <EOS>'
                lineparts[3] += ' <EOS>'
                tgts.append(lineparts[1].strip().split())
                srcs.append(lineparts[3].strip().split())
                wasNew = False
                continue
            if line != '\n' and line != '\r\n':
                aliTXT += line
            else:
                wasNew = True
        if len(aliTXT) > 0:
            c = StringIO(aliTXT)
            ali = np.loadtxt(c)
            ali = ali.transpose()
            alis.append(ali)
            aliTXT = ''
    return srcs, tgts, alis

def main(argv):
    try:
        opts, args = getopt.getopt(argv,"hi:o:s:t:f:")
    except getopt.GetoptError:
        print ('process_alignments.py -i <input_file> -o <output_type> -s <source_sentence_file> -t <target_sentence_file> -f <from_system>')
        print ('outputType can be block or color')
        sys.exit(2)
    for opt, arg in opts:
        if opt == '-h':
            print ('process_alignments.py -i <input_file> -o <output_type> -s <source_sentence_file> -t <target_sentence_file> -f <from_system>')
            print ('outputType can be web, block, block2, color')
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
    try:
        inputfile
    except NameError:
        print ('Provide an input file!')
        print ('process_alignments.py -i <input_file> -o <output_type> -s <source_sentence_file> -t <target_sentence_file> -f <from_system>')
        print ('output_type can be web (default), block, block2 or color')
        print ('from_system can be Nematus or NeuralMonkey (default)')
        sys.exit()
    try:
        from_system
    except NameError:
        from_system = 'NeuralMonkey'
    try:
        outputType
    except NameError:
        # Set output type to 'web' by default
        outputType = 'web'
    if from_system == 'NeuralMonkey':
        try:
            sourcefile
        except NameError:
            print ('Provide an source sentence file!')
            print ('process_alignments.py -i <input_file> -o <output_type> -s <source_sentence_file> -t <target_sentence_file> -f <from_system>')
            print ('output_type can be web (default), block, block2 or color')
            print ('from_system can be Nematus or NeuralMonkey (default)')
            sys.exit()
        try:
            targetfile
        except NameError:
            print ('Provide an target sentence file!')
            print ('process_alignments.py -i <input_file> -o <output_type> -s <source_sentence_file> -t <target_sentence_file> -f <from_system>')
            print ('output_type can be web (default), block, block2 or color')
            print ('from_system can be Nematus or NeuralMonkey (default)')
            sys.exit()
    if outputType != 'color' and outputType != 'block' and outputType != 'block2':
        # Set output type to 'web' by default
        outputType = 'web'

    if from_system == "NeuralMonkey":
        srcs = readSnts(sourcefile)
        tgts = readSnts(targetfile)
        alis = np.load(inputfile)
    if from_system == "Nematus":
        (srcs, tgts, alis) = readNematus(inputfile)

    commonData = list(zip(srcs, tgts, alis))

    foldername = ntpath.basename(inputfile).replace(".","") + "_" + strftime("%d%m_%H%M", gmtime())
    folder = './web/data/' + foldername
    try:
        os.stat(folder)
    except:
        os.mkdir(folder)
        
    with open(folder + "/" + ntpath.basename(inputfile) + '.ali.js', 'w', encoding='utf-8') as out_a_js:
        with open(folder + "/" + ntpath.basename(inputfile) + '.src.js', 'w', encoding='utf-8') as out_s_js:
            with open(folder + "/" + ntpath.basename(inputfile) + '.trg.js', 'w', encoding='utf-8') as out_t_js:
                out_a_js.write(u'var alignments = [\n')
                out_s_js.write(u'var sources = [\n')
                out_t_js.write(u'var targets = [\n')
                for i in range(0, len(commonData)):
                    (src, tgt, rawAli) = commonData[i]
                        
                    ssentence = " ".join(src)
                    tsentence = " ".join(tgt)
                    out_s_js.write('["'+ ssentence.replace(' ','", "') +'"], \n')
                    out_t_js.write('["'+ tsentence.replace(' ','", "') +'"], \n')
                    
                    if outputType == 'web' and from_system == 'Nematus':
                        rawAli = rawAli.transpose()
                        ali = [l[:len(src)] for l in rawAli[:len(tgt)]]
                    else:
                        ali = [l[:len(tgt)] for l in rawAli[:len(src)]]
                    
                    word = 0
                    out_a_js.write(u'[')
                    for ali_i in ali:
                        linePartC=0
                        for ali_j in ali_i:
                            if from_system == 'Nematus':
                                out_a_js.write(u'['+repr(linePartC)+u', ' + str(np.round(ali_j, 8)) + u', '+repr(word)+u'], ')
                            else:
                                out_a_js.write(u'['+repr(word)+u', ' + str(np.round(ali_j, 8)) + u', '+repr(linePartC)+u'], ')
                            linePartC+=1
                            if outputType == 'color':
                                printColor(ali_j)
                            elif outputType == 'block':
                                printBlock(ali_j)
                            elif outputType == 'block2':
                                printBlock2(ali_j)
                        if outputType != 'web':
                            sys.stdout.write(src[word])
                        word+=1
                        if outputType != 'web':
                            sys.stdout.write('\n')
                    
                    # write target sentences
                    #build 2d array
                    occupied_to = []
                    outchars = []
                    outchars.append([])
                    tw = 0
                    for tword in tgt:
                        columns = len(tgt)
                        # Some characters use multiple symbols. Need to decode and then encode...
                        twchars = list(tword)
                        twlen = len(twchars)
                        xpos = tw * 2
                        emptyline = 0
                        
                        for el in range(0, len(occupied_to)):
                            # if occupied, move to a new line!
                            if occupied_to[el] < xpos:
                                emptyline = el
                                if len(outchars) < emptyline+1:
                                    # add a new row
                                    outchars.append([])
                                break
                            if el == len(occupied_to)-1:
                                emptyline=el+1
                                if len(outchars) < emptyline+1:
                                    outchars.append([])
                                 
                        for column in range(0, xpos):
                            if len(outchars[emptyline]) <= column:
                                outchars[emptyline].append(' ')

                        for charindex in range(0, twlen):
                            if xpos+charindex == len(outchars[emptyline]):
                                outchars[emptyline].append(twchars[charindex])
                            else:
                                outchars[emptyline][charindex] = twchars[charindex]
                                                         
                        if len(occupied_to) <= emptyline:
                            occupied_to.append(xpos+twlen+1)
                        else:
                            occupied_to[emptyline]=xpos+twlen+1;
                   
                        tw+=1

                    #print 2d array
                    if outputType != 'web':
                        for liline in outchars:
                            sys.stdout.write(''.join(liline) + '\n')
                   
                    # write target sentences
                    word = 0
                    wasNew = True
                    out_a_js.write(u'], \n')
                    if outputType != 'web':
                        sys.stdout.write('\n')
                out_a_js.write(u'\n]')
                out_s_js.write(u']')
                out_t_js.write(u']')
            
    # Get rid of some junk
    if outputType == 'web':
        webbrowser.open("http://127.0.0.1:47155/?directory=" + foldername)
        os.system("php -S 127.0.0.1:47155 -t web")
    else:
        os.remove(folder + "/" + ntpath.basename(inputfile) + '.ali.js')
        os.remove(folder + "/" + ntpath.basename(inputfile) + '.src.js')
        os.remove(folder + "/" + ntpath.basename(inputfile) + '.trg.js')
        os.rmdir(folder)

if __name__ == "__main__":
    main(sys.argv[1:])
