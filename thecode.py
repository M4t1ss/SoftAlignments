import math

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
