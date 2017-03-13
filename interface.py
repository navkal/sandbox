import argparse
import json
import printctl

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

    classname = types[ args.type ]

    if classname:
      obj = eval( 'queries.' + classname + '( args.id )' )
    else:
      obj = '{ "error": "unknown type: <' + args.type + '>" }'

    printctl.on( )
    print( json.dumps( obj.__dict__ ) )
