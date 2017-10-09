# coding: utf-8

import unicodedata, re, functions, sys, getopt, string, os, webbrowser, math, ntpath, numpy as np
from time import gmtime, strftime
from io import open, StringIO
from imp import reload

def main(argv):
    try:
        opts, args = getopt.getopt(argv,"hi:o:s:t:f:n:")
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
    if outputType != 'color' and outputType != 'block' and outputType != 'block2':
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

    foldername = ntpath.basename(inputfile).replace(".","") + "_" + strftime("%d%m_%H%M", gmtime())
    folder = './web/data/' + foldername
    try:
        os.stat(folder)
    except:
        os.mkdir(folder)
        
    with open(folder + "/" + ntpath.basename(inputfile) + '.ali.js', 'w', encoding='utf-8') as out_a_js:
        with open(folder + "/" + ntpath.basename(inputfile) + '.src.js', 'w', encoding='utf-8') as out_s_js:
            with open(folder + "/" + ntpath.basename(inputfile) + '.trg.js', 'w', encoding='utf-8') as out_t_js:
                with open(folder + "/" + ntpath.basename(inputfile) + '.con.js', 'w', encoding='utf-8') as out_c_js:
                    with open(folder + "/" + ntpath.basename(inputfile) + '.sc.js', 'w', encoding='utf-8') as out_sc_js:
                        out_a_js.write(u'var alignments = [\n')
                        out_s_js.write(u'var sources = [\n')
                        out_t_js.write(u'var targets = [\n')
                        out_c_js.write(u'var confidences = [\n')
                        out_sc_js.write(u'var sentence_confidences = [\n')
                        num = int(num) - 1
                        if num > -1 and (num < len(data)):
                            data = [data[num]]
                        for i in range(0, len(data)):
                            (src, tgt, rawAli) = data[i]
                            ali = [l[:len(tgt)] for l in rawAli[:len(src)]]
                            
                            srcTotal = []
                            trgTotal = []
                            tali = np.array(ali).transpose()
                            for a in range(0, len(ali)):
                                srcTotal.append(str(math.pow(math.e, -0.05 * math.pow((functions.getCP([ali[a]]) + functions.getEnt([ali[a]]) + functions.getRevEnt([ali[a]])), 2))))
                            for a in range(0, len(tali)):
                                trgTotal.append(str(math.pow(math.e, -0.05 * math.pow((functions.getCP([tali[a]]) + functions.getEnt([tali[a]]) + functions.getRevEnt([tali[a]])), 2))))
                            
                            JoinedSource = " ".join(src)
                            JoinedTarget = " ".join(tgt)
                            StrippedSource = ''.join(c for c in JoinedSource if unicodedata.category(c).startswith('L')).replace('EOS','')
                            StrippedTarget = ''.join(c for c in JoinedTarget if unicodedata.category(c).startswith('L')).replace('EOS','')
                            
                            #Get the confidence metrics
                            CDP = round(functions.getCP(ali), 10)
                            APout = round(functions.getEnt(ali), 10)
                            APin = round(functions.getRevEnt(ali), 10)
                            Total = round(CDP + APout + APin, 10)
                            
                            similarity = functions.similar(StrippedSource, StrippedTarget)
                            if similarity > 0.7:
                                Total = round(CDP + APout + APin + (4 * math.tan(similarity)), 10)
                            
                            # e^(-1(x^2))
                            CDP_pr = round(math.pow(math.e, -1 * math.pow(CDP, 2)) * 100, 2)
                            # e^(-0.05(x^2))
                            APout_pr = round(math.pow(math.e, -0.05 * math.pow(APout, 2)) * 100, 2)
                            APin_pr = round(math.pow(math.e, -0.05 * math.pow(APin, 2)) * 100, 2)
                            Total_pr = round(math.pow(math.e, -0.05 * math.pow(Total, 2)) * 100, 2)
                            # 1-e^(-0.0001(x^2))
                            Len = round((1-math.pow(math.e, -0.0001 * math.pow(len(JoinedSource), 2))) * 100, 2)
                            
                            
                            out_s_js.write('["'+ JoinedSource.replace(' ','", "') +'"], \n')
                            out_t_js.write('["'+ JoinedTarget.replace(' ','", "') +'"], \n')
                            out_c_js.write(u'['+ repr(CDP_pr) + u', '+ repr(APout_pr) + u', '+ repr(APin_pr) + u', '+ repr(Total_pr) 
                                + u', '+ repr(Len) + u', '+ repr(len(JoinedSource)) + u', '
                                + repr(round(similarity, 2)) 
                                + u'], \n')
                            out_sc_js.write(u'[[' + ", ".join(srcTotal) + u'], ' + u'[' + ", ".join(trgTotal) + u'], ' + u'], \n')
                            
                            word = 0
                            out_a_js.write(u'[')
                            for ali_i in ali:
                                linePartC=0
                                for ali_j in ali_i:
                                    out_a_js.write(u'['+repr(word)+u', ' + str(np.round(ali_j, 8)) + u', '+repr(linePartC)+u'], ')
                                    linePartC+=1
                                    if outputType == 'color':
                                        functions.printColor(ali_j)
                                    elif outputType == 'block':
                                        functions.printBlock(ali_j)
                                    elif outputType == 'block2':
                                        functions.printBlock2(ali_j)
                                if outputType != 'web':
                                    sys.stdout.write(src[word].encode('utf-8', errors='replace').decode('utf-8'))
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
                                    sys.stdout.write(''.join(liline).encode('utf-8', errors='replace').decode('utf-8') + '\n')
                                # print scores
                                sys.stdout.write('\nCoverage Deviation Penalty: \t\t' + repr(CDP) + ' (' + repr(CDP_pr) + '%)' + '\n')
                                sys.stdout.write('Input Absentmindedness Penalty: \t' + repr(APin) + ' (' + repr(APin_pr) + '%)' + '\n')
                                sys.stdout.write('Output Absentmindedness Penalty: \t' + repr(APout) + ' (' + repr(APout_pr) + '%)' + '\n')
                                sys.stdout.write('Confidence: \t\t\t\t' + repr(Total) + ' (' + repr(Total_pr) + '%)' + '\n')
                           
                            # write target sentences
                            word = 0
                            out_a_js.write(u'], \n')
                            if outputType != 'web':
                                sys.stdout.write('\n')
                        out_a_js.write(u'\n]')
                        out_s_js.write(u']')
                        out_t_js.write(u']')
                        out_c_js.write(u']')
                        out_sc_js.write(u']')
            
    # Get rid of some junk
    if outputType == 'web':
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
