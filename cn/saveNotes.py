# Copyright 2017 Energize Apps.  All rights reserved.

import printctl
import argparse
import json

printctl.off()
import sql

if __name__ == '__main__':
    parser = argparse.ArgumentParser( description='retrieve object from Location Dictionary database' )
    parser.add_argument( '-t', '--table', dest='table', help='object table' )
    parser.add_argument( '-i', '--id', dest='id',  help='object id' )
    parser.add_argument( '-p', '--path', dest='path',  help='object path' )
    parser.add_argument( '-n', '--notes', dest='notes',  help='notes' )
    args = parser.parse_args()

    if args.path:
      selector = 'path="' + args.path + '"'
    elif args.id:
      selector = 'id=' + args.id

    if selector:
      dict = { 'status': 'STUB: Saving notes [' + args.notes + '] at selector [' + selector + '] in table [' + args.table + ']' }
    else:
      dict = { 'status': 'Error: Missing selector argument, either -p or -i' }


    printctl.on( )
    print( json.dumps( dict ) )
