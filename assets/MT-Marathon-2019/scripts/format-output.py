import sys

translation = sys.argv[1]
source = sys.argv[2]

counter = 0
with open(translation) as t, open(source) as s:
    while True:
        counter+=1
        
        source_line = s.readline().strip()
        target_line = t.readline().strip()
        srclen = len(source_line.split())
        
        if srclen == 0 or len(target_line) == 0:
            sys.stderr.write("All done!\n")
            break
            
        target_output, alignment = target_line.split("|||")
        target_output = target_output.strip()
        alignment = alignment.strip()
        trglen = len(target_output.split())
        
        if len(target_output) > 1:
            alignment = "\n".join([" ".join(line.split(",")[:-1]) for line in alignment.split()[:-1]])
            sys.stdout.write("{} ||| {} ||| 0 ||| {} ||| {} ||| {}\n".format(counter,target_output, source_line, srclen, trglen))
            sys.stdout.write(alignment + "\n\n")