# coding: utf-8

import math, re, sys, string, os, ntpath, numpy as np
from time import gmtime, strftime
from io import open, StringIO
from imp import reload
from collections import Counter
from difflib import SequenceMatcher

WORD = re.compile(r'\w+')

def getCP(ali, w = 6):
	l = len(ali)
	if l == 0:
		l = 1
	
	result = 0.0
	
	for ali_i in ali:
		s = sum(ali_i)
		
		pen = 1/ (1 + (abs(1 - s))**w)
		
		result += math.log(pen)
	return result / l

def getEnt(ali):
	l = len(ali)
	if l == 0:
		l = 1
	
	res = 0.0
	
	for pd in ali:
		norm = sum(pd)
		normPd = [p / norm for p in pd]
		entr = -sum([(p * math.log(p) if p else 0) for p in normPd])
		res -= entr
	
	return res / l

def getRevEnt(ali, w = 0.1):
	return getEnt(list(zip(*ali)))

def printHelp():
    print ('process_alignments.py -i <input_file> [-o <output_type>] [-f <from_system>] [-s <source_sentence_file>] [-t <target_sentence_file>]')
    print ('input_file is the file with alignment weights (required)')
    print ('source_sentence_file and target_sentence_file are required only for NeuralMonkey')
    print ('output_type can be web (default), block, block2 or color')
    print ('from_system can be Nematus, AmuNMT, Sockeye,  OpenNMT or NeuralMonkey (default)')

def printColor(value):
    colors = [
        '[48;5;232m[K  [m[K',
        '[48;5;233m[K  [m[K',
        '[48;5;234m[K  [m[K',
        '[48;5;235m[K  [m[K',
        '[48;5;236m[K  [m[K',
        '[48;5;237m[K  [m[K',
        '[48;5;238m[K  [m[K',
        '[48;5;239m[K  [m[K',
        '[48;5;240m[K  [m[K',
        '[48;5;240m[K  [m[K',
        '[48;5;241m[K  [m[K',
        '[48;5;242m[K  [m[K',
        '[48;5;243m[K  [m[K',
        '[48;5;244m[K  [m[K',
        '[48;5;245m[K  [m[K',
        '[48;5;246m[K  [m[K',
        '[48;5;247m[K  [m[K',
        '[48;5;248m[K  [m[K',
        '[48;5;249m[K  [m[K',
        '[48;5;250m[K  [m[K',
        '[48;5;251m[K  [m[K',
        '[48;5;252m[K  [m[K',
        '[48;5;253m[K  [m[K',
        '[48;5;254m[K  [m[K',
        '[48;5;255m[K  [m[K',
        '[48;5;255m[K  [m[K',
    ]
    num = int(math.floor((value-0.01)*25))
    if num<0: num = 0
    sys.stdout.write(colors[num])

def printBlock2(value):
    blocks2 = ['██', '▉▉', '▊▊', '▋▋', '▌▌', '▍▍', '▎▎', '▏▏', '  ',]
    num = int(math.floor((value-0.01)*8))
    if num<0: num = 0
    sys.stdout.write(blocks2[num])
        
def printBlock(value):
    blocks = ['██', '▓▓', '▒▒', '░░', '  ',]
    num = int(math.floor((value-0.01)*4))
    if num<0: num = 0
    sys.stdout.write(blocks[num])
        
def readSnts(filename):
    with open(filename, 'r', encoding='utf-8') as fh:
        return [escape(line).strip().split() for line in fh]

def readNematus(filename, openNMT = 0):
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
                    if openNMT == 0:
                        ali = ali.transpose()
                    alis.append(ali)
                    aliTXT = ''
                lineparts = line.split(' ||| ')
                if openNMT == 0:
                    lineparts[1] += ' <EOS>'
                    lineparts[3] += ' <EOS>'
                tgts.append(escape(lineparts[1]).strip().split())
                srcs.append(escape(lineparts[3]).strip().split())
                wasNew = False
                continue
            if line != '\n' and line != '\r\n':
                aliTXT += line
            else:
                wasNew = True
        if len(aliTXT) > 0:
            c = StringIO(aliTXT)
            ali = np.loadtxt(c)
            if openNMT == 0:
                ali = ali.transpose()
            alis.append(ali)
            aliTXT = ''
    return srcs, tgts, alis
    
def escape(string):
    return string.replace('"','&quot;').replace("'","&apos;")
    
def readAmu(in_file, src_file):
    with open(src_file, 'r', encoding='utf-8') as fi:
        with open(in_file, 'r', encoding='utf-8') as fh:
            alis = []
            tgts = []
            srcs = []
            aliTXT = ''
            for src_line, out_line in izip(fi, fh):
                lineparts = out_line.split(' ||| ')
                lineparts[0] += ' <EOS>'
                src_line = src_line.strip() + ' <EOS>'
                tgts.append(escape(lineparts[0]).strip().split())
                srcs.append(escape(src_line).split())
                #alignment weights
                weightparts = lineparts[1].split(') | (')
                for weightpart in weightparts:
                    aliTXT += weightpart.replace('(','') + '\n'
                if len(aliTXT) > 0:
                    c = StringIO(aliTXT.replace(' ) | ',''))
                    ali = np.loadtxt(c)
                    ali = ali.transpose()
                    alis.append(ali)
                    aliTXT = ''
    return srcs, tgts, alis

def get_cosine(vec1, vec2):
	intersection = set(vec1.keys()) & set(vec2.keys())
	numerator = sum([vec1[x] * vec2[x] for x in intersection])

	sum1 = sum([vec1[x]**2 for x in vec1.keys()])
	sum2 = sum([vec2[x]**2 for x in vec2.keys()])
	denominator = math.sqrt(sum1) * math.sqrt(sum2)

	if not denominator:
		return 0.0
	else:
		return float(numerator) / denominator

def text_to_vector(text):
	words = WORD.findall(text)
	return Counter(words)

def similar(a, b):
    return SequenceMatcher(None, a, b).ratio()