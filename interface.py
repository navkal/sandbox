import printctl
import argparse
import json

printctl.off()
import queries

if __name__ == '__main__':
    parser = argparse.ArgumentParser( description='retrieve object from Location Dictionary database' )
    parser.add_argument( '-t', '--type', dest='type', help='object type' )
    parser.add_argument( '-i', '--id', dest='id',  help='object id' )
    args = parser.parse_args()

    types = {
      'device': 'device',
      'circuit': 'cirobj',
      'room': 'room' }

    try:
      classname = types[ args.type ]
      object = eval( 'queries.' + classname + '( args.id )' )
      dict = object.__dict__;
    except:
      dict = { 'error': 'unrecognized type [' + args.type + ']' }

    printctl.on( )
    print( json.dumps( dict ) )
