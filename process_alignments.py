# -*- coding: utf-8 -*-

import sys, getopt
import numpy as np
import string
import os

def main(argv):
   try:
      opts, args = getopt.getopt(argv,"hi:o:s:t:f:")
   except getopt.GetoptError:
      print 'process_alignments.py -i <input_file> -o <output_type> -s <source_sentence_file> -t <target_sentence_file>'
      print 'outputType can be block or color'
      sys.exit(2)
   for opt, arg in opts:
      if opt == '-h':
         print 'process_alignments.py -i <input_file> -o <output_type> -s <source_sentence_file> -t <target_sentence_file>'
         print 'outputType can be web, block, block2, color'
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
     print "Provide an input file!"
     print 'process_alignments.py -i <input_file> -o <output_type> -s <source_sentence_file> -t <target_sentence_file> -f <from_system>'
     print 'output_type can be web (default), block, block2 or color'
     print 'from_system can be Nematus or NeuralMonkey (default)'
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
         print "Provide an source sentence file!"
         print 'process_alignments.py -i <input_file> -o <output_type> -s <source_sentence_file> -t <target_sentence_file> -f <from_system>'
         print 'output_type can be web (default), block, block2 or color'
         print 'from_system can be Nematus or NeuralMonkey (default)'
         sys.exit()
       try:
         targetfile
       except NameError:
         print "Provide an target sentence file!"
         print 'process_alignments.py -i <input_file> -o <output_type> -s <source_sentence_file> -t <target_sentence_file> -f <from_system>'
         print 'output_type can be web (default), block, block2 or color'
         print 'from_system can be Nematus or NeuralMonkey (default)'
         sys.exit()
   if outputType != 'color' and outputType != 'block' and outputType != 'block2':
     # Set output type to 'web' by default
     outputType = 'web'

   if from_system == 'NeuralMonkey':
       data = np.load(inputfile)

       # Read source and target sentences
       sourcelines = [line.rstrip('\n') for line in open(sourcefile)]
       targetlines = [line.rstrip('\n') for line in open(targetfile)]

       with file(inputfile + '.txt', 'w') as outfile:
           for data_slice in data:

               # values in left-justified columns 7 characters in width
               # with 2 decimal places.  
               np.savetxt(outfile, data_slice, fmt='%-7.2f')

               # a break to indicate different sentences...
               outfile.write('\n')
   
   if from_system == 'NeuralMonkey':
        inputfileName = inputfile
        inputfile = inputfile + '.txt'
            
   with open(inputfile) as infile:
        with open(inputfileName + '.' + outputType +'.txt', 'w') as outfile:
            with open(inputfileName + '.ali.js', 'w') as out_a_js:
                if outputType == 'web':
                    out_a_js.write('var alignments = [\n')
                sent = 0
                word = 0
                wasNew = True
                atEnd = False
                if from_system == 'Nematus':
                    sourcelines = []
                    targetlines = []
                if from_system == 'NeuralMonkey':
                    stokens = sourcelines[sent].split(' ')
                    ttokens = targetlines[sent].split(' ')

                #height
                for line in infile:
                   if wasNew:
                      if from_system == 'Nematus':
                           lineparts = line.split(' ||| ')
                           sourceline = lineparts[1]
                           targetline = lineparts[3]
                           sourcelines.append(sourceline + ' <EOS>')
                           targetlines.append(targetline + ' <EOS>')
                           stokens = sourceline.split(' ')
                           ttokens = targetline.split(' ')
                           stokens.append('<EOS>')
                           ttokens.append('<EOS>')
                           if outputType == 'web':
                               out_a_js.write('[')
                           wasNew = False
                           continue
                      elif from_system == 'NeuralMonkey':
                       if outputType == 'web':
                           out_a_js.write('[')
                       wasNew = False
                   if line != '\n' and line != '\r\n':
                       if word > len(stokens)-1:
                           continue
                       lineParts = line.split()
                       linePartC=0
                       #width
                       for linePart in lineParts:
                           if linePartC > len(ttokens)-1:
                               continue
                           if linePartC < len(lineParts) and linePart.replace("  ", " ").replace("  ", " ").replace("  ", " ") != "":
                               if outputType == 'web':
                                   out_a_js.write('['+`word`+', ' + linePart + ', '+`linePartC`+'], ')
                               linePartC+=1
                           if outputType == 'color':
                               if float(linePart) == 0:
                                   outfile.write('[48;5;232m[K  [m[K' )
                               elif float(linePart) > 0.00 and float(linePart) <= 0.04:
                                   outfile.write('[48;5;233m[K  [m[K' )
                               elif float(linePart) > 0.04 and float(linePart) <= 0.08:
                                   outfile.write('[48;5;234m[K  [m[K' )
                               elif float(linePart) > 0.08 and float(linePart) <= 0.13:
                                   outfile.write('[48;5;235m[K  [m[K' )
                               elif float(linePart) > 0.13 and float(linePart) <= 0.17:
                                   outfile.write('[48;5;236m[K  [m[K' )
                               elif float(linePart) > 0.17 and float(linePart) <= 0.21:
                                   outfile.write('[48;5;237m[K  [m[K' )
                               elif float(linePart) > 0.21 and float(linePart) <= 0.25:
                                   outfile.write('[48;5;238m[K  [m[K' )
                               elif float(linePart) > 0.25 and float(linePart) <= 0.29:
                                   outfile.write('[48;5;239m[K  [m[K' )
                               elif float(linePart) > 0.29 and float(linePart) <= 0.33:
                                   outfile.write('[48;5;240m[K  [m[K' )
                               elif float(linePart) > 0.33 and float(linePart) <= 0.38:
                                   outfile.write('[48;5;241m[K  [m[K' )
                               elif float(linePart) > 0.38 and float(linePart) <= 0.42:
                                   outfile.write('[48;5;242m[K  [m[K' )
                               elif float(linePart) > 0.42 and float(linePart) <= 0.46:
                                   outfile.write('[48;5;243m[K  [m[K' )
                               elif float(linePart) > 0.46 and float(linePart) <= 0.50:
                                   outfile.write('[48;5;244m[K  [m[K' )
                               elif float(linePart) > 0.50 and float(linePart) <= 0.54:
                                   outfile.write('[48;5;245m[K  [m[K' )
                               elif float(linePart) > 0.54 and float(linePart) <= 0.58:
                                   outfile.write('[48;5;246m[K  [m[K' )
                               elif float(linePart) > 0.58 and float(linePart) <= 0.63:
                                   outfile.write('[48;5;247m[K  [m[K' )
                               elif float(linePart) > 0.63 and float(linePart) <= 0.67:
                                   outfile.write('[48;5;248m[K  [m[K' )
                               elif float(linePart) > 0.67 and float(linePart) <= 0.71:
                                   outfile.write('[48;5;249m[K  [m[K' )
                               elif float(linePart) > 0.71 and float(linePart) <= 0.75:
                                   outfile.write('[48;5;250m[K  [m[K' )
                               elif float(linePart) > 0.75 and float(linePart) <= 0.79:
                                   outfile.write('[48;5;251m[K  [m[K' )
                               elif float(linePart) > 0.79 and float(linePart) <= 0.83:
                                   outfile.write('[48;5;252m[K  [m[K' )
                               elif float(linePart) > 0.83 and float(linePart) <= 0.88:
                                   outfile.write('[48;5;253m[K  [m[K' )
                               elif float(linePart) > 0.88 and float(linePart) <= 0.92:
                                   outfile.write('[48;5;254m[K  [m[K' )
                               elif float(linePart) > 0.92 and float(linePart) <= 0.96:
                                   outfile.write('[48;5;255m[K  [m[K' )
                               elif float(linePart) > 0.96 and float(linePart) <= 1:
                                   outfile.write('[48;5;255m[K  [m[K' )
                               else:
                                   outfile.write('[48;5;232m[K  [m[K' )
                           elif outputType == 'block':
                               if float(linePart) == 0:
                                   outfile.write('██')
                               elif float(linePart) > 0 and float(linePart) <= 0.25:
                                   outfile.write('▓▓')
                               elif float(linePart) > 0.25 and float(linePart) <= 0.5:
                                   outfile.write('▒▒')
                               elif float(linePart) > 0.5 and float(linePart) <= 0.75:
                                   outfile.write('░░')
                               elif float(linePart) > 0.75 and float(linePart) <= 1:
                                   outfile.write('  ')
                               else:
                                   outfile.write('██')
                           elif outputType == 'block2':
                               if float(linePart) == 0:
                                   outfile.write('██')
                               elif float(linePart) > 0 and float(linePart) <= 0.125:
                                   outfile.write('▉▉')
                               elif float(linePart) > 0.125 and float(linePart) <= 0.250:
                                   outfile.write('▊▊')
                               elif float(linePart) > 0.250 and float(linePart) <= 0.375:
                                   outfile.write('▋▋')
                               elif float(linePart) > 0.375 and float(linePart) <= 0.500:
                                   outfile.write('▌▌')
                               elif float(linePart) > 0.500 and float(linePart) <= 0.625:
                                   outfile.write('▍▍')
                               elif float(linePart) > 0.625 and float(linePart) <= 0.750:
                                   outfile.write('▎▎')
                               elif float(linePart) > 0.750 and float(linePart) <= 0.875:
                                   outfile.write('▏▏')
                               elif float(linePart) > 0.875 and float(linePart) <= 1:
                                   outfile.write('  ')
                               else:
                                   outfile.write('██')
                       if word < len(stokens) and outputType != 'web':
                           outfile.write(stokens[word])
                       word+=1
                       if outputType != 'web':
                           outfile.write('\n')
                   else:
                       # write target sentences
                       #build 2d array
                       occupied_to = []
                       outchars = []
                       outchars.append([])
                       tw = 0
                       for tword in ttokens:
                            columns = len(ttokens)
                            # Some characters use multiple symbols. Need to decode and then encode...
                            twchars = list(tword.decode("utf-8"))
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
                                    outchars[emptyline].append(twchars[charindex].encode("utf-8"))
                                else:
                                    outchars[emptyline][charindex] = twchars[charindex].encode("utf-8")
                                                           
                            if len(occupied_to) <= emptyline:
                                occupied_to.append(xpos+twlen+1)
                            else:
                                occupied_to[emptyline]=xpos+twlen+1;
                       
                            tw+=1

                       #print 2d array
                       if outputType != 'web':
                           for liline in outchars:
                                outfile.write(''.join(liline) + '\n')
                       
                       # write target sentences
                       sent+=1
                       if from_system == 'NeuralMonkey':
                           if len(sourcelines) >= sent+1 and len(targetlines) >= sent+1:
                               stokens = sourcelines[sent].split(' ')
                               ttokens = targetlines[sent].split(' ')
                       word = 0
                       wasNew = True
                       if outputType == 'web':
                           out_a_js.write('], \n')
                       if outputType != 'web':
                           outfile.write('\n')
                   if atEnd:
                       atEnd = False
                       continue
                if outputType == 'web':
                    out_a_js.write('\n]')
   

   if outputType == 'web':
       with open(inputfileName + '.src.js', 'w') as out_s_js:
            out_s_js.write('var sources = [\n')
            for line in sourcelines:
                out_s_js.write('["'+ line.replace(' ','", "') +'"], \n')
            out_s_js.write(']')
       
       with open(inputfileName + '.trg.js', 'w') as out_t_js:
            out_t_js.write('var targets = [\n')
            for line in targetlines:
                out_t_js.write('["'+ line.replace(' ','", "') +'"], \n')
            out_t_js.write(']')
            
   # Get rid of some junk
   os.remove(inputfile)
   if outputType == 'web':
       os.remove(inputfileName + '.' + outputType + '.txt')
   else:
       os.remove(inputfileName + '.ali.js')

if __name__ == "__main__":
   main(sys.argv[1:])

