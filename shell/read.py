#!/usr/bin/env python
#coding: utf-8
from ConfigParser import ConfigParser
import os
import sys
if len(sys.argv) != 3:
	print "参数不正确"
	sys.exit(1)

path=os.path.abspath(os.path.curdir) 
cf=ConfigParser()
cf.read('/data/vnnox-deploy/shell/config.ini')
if cf.has_section(sys.argv[1]):
	print cf.get(sys.argv[1],sys.argv[2])
