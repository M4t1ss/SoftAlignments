# -*- coding: utf-8 -*-

import sys, getopt
import string

def main(argv):
   try:
      opts, args = getopt.getopt(argv,"hi:")
   except getopt.GetoptError:
      print 'aTXTtoJSON.py -i <input_file>'
      sys.exit(2)
   for opt, arg in opts:
      if opt == '-h':
         print 'aTXTtoJSON.py -i <input_file>'
         sys.exit()
      elif opt == '-i':
         inputfile = arg
   try:
     inputfile
   except NameError:
     print "Provide an input file!"
     print 'aTXTtoJSON.py -i <input_file>'
     sys.exit()

   # Read lines
   sourcelines = open(inputfile)

   with open(inputfile + '.json', 'w') as outfile:
       outfile.write('[\n')
       lineC = 0
       for line in sourcelines:
           if line != '\n' and line != '\r\n':
               outfile.write('[')
               tokenC=0
               tokens = line.replace("  ", " ").replace("  ", " ").replace("  ", " ").replace("\r\n", "").replace("\n", "").split(" ")
               for token in tokens:
                   if tokenC < len(tokens) and token.replace("  ", " ").replace("  ", " ").replace("  ", " ") != "":
                       outfile.write('['+`lineC`+', 0, ' + token + ', '+`tokenC`+', 0], ')
                       tokenC+=1
               lineC+=1
               outfile.write('], ')
           else:
               outfile.write('\n')
               lineC = 0
       outfile.write('\n]')

if __name__ == "__main__":
   main(sys.argv[1:])

