# Copyright 2017 Energize Apps.  All rights reserved.

import printctl
import argparse
import json

printctl.off()
import queries

if __name__ == '__main__':
    parser = argparse.ArgumentParser( description='retrieve object from Location Dictionary database' )
    parser.add_argument( '-t', '--table', dest='table', help='object table' )
    parser.add_argument( '-i', '--id', dest='id',  help='object id' )
    parser.add_argument( '-p', '--path', dest='path',  help='object path' )
    args = parser.parse_args()

    tables = {
      'device': 'device',
      'circuit': 'cirobj',
      'room': 'room' }

    if args.path:
      selector = 'path="' + args.path + '"'
    elif args.id:
      selector = 'id=' + args.id
    else:
      selector = ''

    try:
      classname = tables[ args.table ]
    except:
      dict = { 'Error': 'Unrecognized table [' + args.table + ']' }
    else:
      try:
        object = eval( 'queries.' + classname + '( ' + selector + ' )' )
      except:
        dict = { 'Error': 'Could not retrieve [' + selector + ']' }
      else:
        dict = object.__dict__;

    printctl.on( )
    print( json.dumps( dict ) )
