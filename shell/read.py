#!/usr/bin/env python
#coding: utf-8
from ConfigParser import ConfigParser
import os
import sys
if len(sys.argv) != 3:
        print "参数不正确"
        sys.exit(1)

path=os.path.abspath(os.path.dirname(sys.argv[0]))
#print "path:%s" %path 
cf=ConfigParser()
cf.read(os.path.join(path+'/config.ini'))
if cf.has_section(sys.argv[1]):
        print cf.get(sys.argv[1],sys.argv[2])
