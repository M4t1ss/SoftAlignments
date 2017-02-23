# -*- coding: utf-8 -*-

import sys, getopt
import numpy as np
import string

def main(argv):
   try:
      opts, args = getopt.getopt(argv,"hi:o:s:t:")
   except getopt.GetoptError:
      print 'process_alignments.py -i <input_file> -o <output_type> -s <source_sentence_file> -t <target_sentence_file>'
      print 'outputType can be block or color'
      sys.exit(2)
   for opt, arg in opts:
      if opt == '-h':
         print 'process_alignments.py -i <input_file> -o <output_type> -s <source_sentence_file> -t <target_sentence_file>'
         print 'outputType can be block or color'
         sys.exit()
      elif opt == '-i':
         inputfile = arg
      elif opt == '-o':
         outputType = arg
      elif opt == '-s':
         sourcefile = arg
      elif opt == '-t':
         targetfile = arg
   try:
     inputfile
   except NameError:
     print "Provide an input file!"
     print 'process_alignments.py -i <input_file> -o <output_type> -s <source_sentence_file> -t <target_sentence_file>'
     print 'outputType can be block or color'
     sys.exit()
   try:
     outputType
   except NameError:
     print "Provide an output type!"
     print 'process_alignments.py -i <input_file> -o <output_type> -s <source_sentence_file> -t <target_sentence_file>'
     print 'outputType can be block or color'
     sys.exit()
   try:
     sourcefile
   except NameError:
     print "Provide an source sentence file!"
     print 'process_alignments.py -i <input_file> -o <output_type> -s <source_sentence_file> -t <target_sentence_file>'
     print 'outputType can be block or color'
     sys.exit()
   try:
     targetfile
   except NameError:
     print "Provide an target sentence file!"
     print 'process_alignments.py -i <input_file> -o <output_type> -s <source_sentence_file> -t <target_sentence_file>'
     print 'outputType can be block or color'
     sys.exit()
   if outputType != 'color' and outputType != 'block' and outputType != 'block2':
     print "Provide an output type!"
     print 'outputType must be block or color'
     sys.exit()

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
		
		
   with open(inputfile + '.txt') as infile:
        with open(inputfile + '.' + outputType +'.txt', 'w') as outfile:
            with open(inputfile + '.ali.js', 'w') as out_a_js:
                out_a_js.write('var alignments = [\n')
                sent = 0
                word = 0
                wasNew = True
                atEnd = False
                stokens = sourcelines[sent].split(' ')
                ttokens = targetlines[sent].split(' ')

                #height
                for line in infile:
                   if wasNew:
                       out_a_js.write('[')
                       wasNew = False
                   if line != '\n' and line != '\r\n':
                       if word > len(stokens):
                           continue
                       lineParts = line.split()
                       linePartC=0
                       #width
                       for linePart in lineParts:
                           if linePartC > len(ttokens):
                               continue
                           if linePartC < len(lineParts) and linePart.replace("  ", " ").replace("  ", " ").replace("  ", " ") != "":
                               out_a_js.write('['+`word`+', 0, ' + linePart + ', '+`linePartC`+', 0], ')
                               linePartC+=1
                           if outputType == 'color':
                               if float(linePart) == 0:
                                   outfile.write('[48;5;232m[K [m[K')
                               elif float(linePart) > 0.00 and float(linePart) <= 0.04:
                                   outfile.write('[48;5;233m[K [m[K')
                               elif float(linePart) > 0.04 and float(linePart) <= 0.08:
                                   outfile.write('[48;5;234m[K [m[K')
                               elif float(linePart) > 0.08 and float(linePart) <= 0.13:
                                   outfile.write('[48;5;235m[K [m[K')
                               elif float(linePart) > 0.13 and float(linePart) <= 0.17:
                                   outfile.write('[48;5;236m[K [m[K')
                               elif float(linePart) > 0.17 and float(linePart) <= 0.21:
                                   outfile.write('[48;5;237m[K [m[K')
                               elif float(linePart) > 0.21 and float(linePart) <= 0.25:
                                   outfile.write('[48;5;238m[K [m[K')
                               elif float(linePart) > 0.25 and float(linePart) <= 0.29:
                                   outfile.write('[48;5;239m[K [m[K')
                               elif float(linePart) > 0.29 and float(linePart) <= 0.33:
                                   outfile.write('[48;5;240m[K [m[K')
                               elif float(linePart) > 0.33 and float(linePart) <= 0.38:
                                   outfile.write('[48;5;241m[K [m[K')
                               elif float(linePart) > 0.38 and float(linePart) <= 0.42:
                                   outfile.write('[48;5;242m[K [m[K')
                               elif float(linePart) > 0.42 and float(linePart) <= 0.46:
                                   outfile.write('[48;5;243m[K [m[K')
                               elif float(linePart) > 0.46 and float(linePart) <= 0.50:
                                   outfile.write('[48;5;244m[K [m[K')
                               elif float(linePart) > 0.50 and float(linePart) <= 0.54:
                                   outfile.write('[48;5;245m[K [m[K')
                               elif float(linePart) > 0.54 and float(linePart) <= 0.58:
                                   outfile.write('[48;5;246m[K [m[K')
                               elif float(linePart) > 0.58 and float(linePart) <= 0.63:
                                   outfile.write('[48;5;247m[K [m[K')
                               elif float(linePart) > 0.63 and float(linePart) <= 0.67:
                                   outfile.write('[48;5;248m[K [m[K')
                               elif float(linePart) > 0.67 and float(linePart) <= 0.71:
                                   outfile.write('[48;5;249m[K [m[K')
                               elif float(linePart) > 0.71 and float(linePart) <= 0.75:
                                   outfile.write('[48;5;250m[K [m[K')
                               elif float(linePart) > 0.75 and float(linePart) <= 0.79:
                                   outfile.write('[48;5;251m[K [m[K')
                               elif float(linePart) > 0.79 and float(linePart) <= 0.83:
                                   outfile.write('[48;5;252m[K [m[K')
                               elif float(linePart) > 0.83 and float(linePart) <= 0.88:
                                   outfile.write('[48;5;253m[K [m[K')
                               elif float(linePart) > 0.88 and float(linePart) <= 0.92:
                                   outfile.write('[48;5;254m[K [m[K')
                               elif float(linePart) > 0.92 and float(linePart) <= 0.96:
                                   outfile.write('[48;5;255m[K [m[K')
                               elif float(linePart) > 0.96 and float(linePart) <= 1:
                                   outfile.write('[48;5;255m[K [m[K')
                               else:
                                   outfile.write('[48;5;232m[K [m[K')
                           elif outputType == 'block':
                               if float(linePart) == 0:
                                   outfile.write('█')
                               elif float(linePart) > 0 and float(linePart) <= 0.25:
                                   outfile.write('▓')
                               elif float(linePart) > 0.25 and float(linePart) <= 0.5:
                                   outfile.write('▒')
                               elif float(linePart) > 0.5 and float(linePart) <= 0.75:
                                   outfile.write('░')
                               elif float(linePart) > 0.75 and float(linePart) <= 1:
                                   outfile.write(' ')
                               else:
                                   outfile.write('█')
                           elif outputType == 'block2':
                               if float(linePart) == 0:
                                   outfile.write('█')
                               elif float(linePart) > 0 and float(linePart) <= 0.125:
                                   outfile.write('▉')
                               elif float(linePart) > 0.125 and float(linePart) <= 0.250:
                                   outfile.write('▊')
                               elif float(linePart) > 0.250 and float(linePart) <= 0.375:
                                   outfile.write('▋')
                               elif float(linePart) > 0.375 and float(linePart) <= 0.500:
                                   outfile.write('▌')
                               elif float(linePart) > 0.500 and float(linePart) <= 0.625:
                                   outfile.write('▍')
                               elif float(linePart) > 0.625 and float(linePart) <= 0.750:
                                   outfile.write('▎')
                               elif float(linePart) > 0.750 and float(linePart) <= 0.875:
                                   outfile.write('▏')
                               elif float(linePart) > 0.875 and float(linePart) <= 1:
                                   outfile.write(' ')
                               else:
                                   outfile.write('█')
                       if word < len(stokens):
                           outfile.write(stokens[word])
                       word+=1
                       outfile.write('\n')
                   else:
                       sent+=1
                       if len(sourcelines) >= sent+1 and len(targetlines) >= sent+1:
                           stokens = sourcelines[sent].split(' ')
                           ttokens = targetlines[sent].split(' ')
                       word = 0
                       wasNew = True
                       out_a_js.write('], \n')
                       outfile.write('\n')
                   if atEnd:
                       atEnd = False
                       continue
                out_a_js.write('\n]')
   

   with open(inputfile + '.src.js', 'w') as out_s_js:
        out_s_js.write('var sources = [\n')
        for line in sourcelines:
            out_s_js.write('["'+ line.replace(' ','", "') +'"], \n')
        out_s_js.write(']')
   
   with open(inputfile + '.trg.js', 'w') as out_t_js:
        out_t_js.write('var targets = [\n')
        for line in targetlines:
            out_t_js.write('["'+ line.replace(' ','", "') +'"], \n')
        out_t_js.write(']')

if __name__ == "__main__":
   main(sys.argv[1:])

