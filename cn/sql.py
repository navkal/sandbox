import sqlite3
import os
import time
from eventTypes import dcEventTypes

conn = sqlite3.connect('AHSMap.sqlite')
cur = conn.cursor()

class device:
    def __init__(self,id):
        self.id = id

        #initialize room properties
        cur.execute('SELECT * FROM Device WHERE id = ?', (id,))
        row = cur.fetchone()
        print("I am a device. This is me:", row)

        self.room_id = row[1]
        self.panel_id = row[2]
        self.description = row[3]
        self.parent_path = row[5] # For tree structure
        self.source = row[5] # For properties display

        #gets room where device is located
        print( 'room-id', self.room_id )
        if str( self.room_id ).isdigit():
            cur.execute('SELECT room_num, old_num FROM Room WHERE id = ?', (self.room_id,))
            rooms = cur.fetchone()
            self.loc_new = rooms[0]
            if (self.loc_new == 'no new room') or (self.loc_new.upper().find( 'UNKNOWN' ) != -1):
                self.loc_new = ''
            self.loc_old = rooms[1]
            if (self.loc_old == 'no old room') or (self.loc_old.upper().find( 'UNKNOWN' ) != -1):
                self.loc_old = ''
        else:
            self.loc_new = ''
            self.loc_old = ''


        cur.execute( "SELECT timestamp, username, event_type, description FROM Activity WHERE target_table = 'Device' AND target_column = 'id' AND target_value = ?", (self.id,) )
        print( 'id=', self.id )
        self.events = cur.fetchall()
        print( 'events=', self.events )


    def properties(self):
        print("room_id:", self.room_id)
        print("panel_id:",self.panel_id)
        print("description:", self.description)
        print("parent_path:",self.parent_path)
        print("loc_new:", self.loc_new)
        print("loc_old:", self.loc_old)

    def get_main_display(self):
        return {'ID': self.id,
                'Room ID': self.room_id,
                'Panel ID': self.panel_id,
                'Description':self.description,
                'Parent Path': self.parent_path,
                'Location New': self.loc_new,
                'Location Old': self.loc_old}


class cirobj:

    def __init__(self,id=None,path=None):
        if id:
            cur.execute('SELECT * FROM CircuitObject WHERE id = ?', (id,))
        elif path:
            cur.execute('SELECT * FROM CircuitObject WHERE upper(path) = ?', (path.upper(),))
        else:
            cur.execute('SELECT * FROM CircuitObject WHERE path NOT LIKE "%.%"' )

        #initialize circuitObject properties
        row = cur.fetchone()
        cur.execute('SELECT * FROM Voltage WHERE id = ?',(row[4],))
        voltage = cur.fetchone()
        print(row,voltage)

        self.id = row[0]
        self.room_id = row[1]
        self.path = row[2]
        self.voltage = voltage[1]
        self.object_type = row[5].title()
        self.description = row[6]
        self.parent_path = row[7]

        # Get room information
        cur.execute('SELECT * FROM Room WHERE id = ?', (self.room_id,))
        room = cur.fetchone()
        print(room)
        self.loc_new = room[1]
        if self.loc_new == 'no new room':
            self.loc_new = ''
        self.loc_old = room[2]
        if self.loc_old == 'no old room':
            self.loc_old = ''
        self.loc_type = room[3]
        if self.loc_type == 'no data':
            self.loc_type = ''
        self.loc_descr = room[4]

        # Add image filename
        filename = 'images/' + self.path + '.jpg'
        if os.path.isfile( filename ):
            self.image = filename
        else:
            self.image = ''

        # Retrieve children
        cur.execute('SELECT id, path, description, object_type FROM CircuitObject WHERE parent = ?', (self.path,))
        self.children = cur.fetchall()

        # Append child image filenames
        for i in range( len( self.children ) ):
            filename = 'images/' + self.children[i][1] + '.jpg'
            if os.path.isfile( filename ):
                self.children[i] = self.children[i] + ( filename, )
            else:
                self.children[i] = self.children[i] + ('',)

        cur.execute('SELECT id FROM Device WHERE parent = ?', (self.path,))
        dev_ids = cur.fetchall()
        self.devices = []
        for i in range( len (dev_ids) ):
            dev_id = dev_ids[i][0]
            dev = device( dev_id )
            self.devices.append( [ dev.id, dev.loc_new, dev.loc_old, dev.description ] )


        cur.execute( "SELECT timestamp, username, event_type, description FROM Activity WHERE target_table = 'CircuitObject' AND target_column = 'path' AND target_value = ?", (self.path,) )
        self.events = cur.fetchall()

        print('my parent path is ',self.parent_path)
        print('my children are ', self.children)
        print('my devices are ', self.devices)
        print( 'my events are ', self.events )


    def get_main_display(self):
        return {'ID': self.id,
                'Room ID': self.room_id,
                'Path': self.path,
                'Voltage': self.voltage,
                'Type': self.object_type,
                'Description': self.description,
                'Parent Path': self.parent_path,
                'Children': self.children}

class room:

    def __init__(self,id):
        self.id = id

        #initialize room properties
        cur.execute('SELECT * FROM Room WHERE id = ?', (id,))
        row = cur.fetchone()
        print("I'm a room. This is me:", row)

        self.oldnum = row[2]
        self.newnum = row[1]
        self.description = row[4]


        self.devices = []
        cur.execute('SELECT * FROM Device WHERE room_id = ?', (self.id,))
        rows = cur.fetchall()
        for r in rows:
            #print('this is a device', r)
            self.devices.append(r)


        self.cirobjs = []
        cur.execute('SELECT * FROM CircuitObject WHERE room_id = ?', (self.id,))
        rows = cur.fetchall()
        for r in rows:
            #print('this is a cirobj', r)
            self.cirobjs.append(r)


        #if there is 1 or more cirobj in a room, it is a closet
        self.closet = len(self.cirobjs) >= 1
        if self.closet == True:
            print('I\'m a closet')

        else:
            print('I\'m not a closet')

    def properties(self):
        print("My old room number is ", self.oldnum)
        print("My new room number is ", self.newnum)
        print("It's " + str(self.closet) + " that I'm a closet")
        print("The description of my room is ", self.description)
        print("These are the devices in my room:", self.devices)
        print("these are my Circuit Objects:", self.cirobjs)


    def get_main_display(self):
        return {'ID': self.id,
                'Old Number': self.oldnum,
                'New Number': self.newnum,
                'Description': self.description,
                'Devices': self.devices,
                'Circuit Objects': self.cirobjs,
                'Closet': self.closet
                }

class search:
    def __init__(self, searchText):
        print( 'search text=<' + searchText + '>' )
        #cur.execute('SELECT * FROM (SELECT path, description FROM CircuitObject WHERE description LIKE "%' + searchText + '%") LIMIT 5')

        cur.execute('SELECT path, path FROM CircuitObject WHERE tail LIKE "%' + searchText + '%"')
        pathRows = cur.fetchall()

        cur.execute('SELECT path, description FROM CircuitObject WHERE description LIKE "%' + searchText + '%"')
        descrRows = cur.fetchall()

        self.searchResults = pathRows + descrRows

        print('found ' + str(len(self.searchResults)) + ' matches' )


class saveNotes:
    def __init__(self, args):

        cur.execute('''INSERT INTO Activity ( timestamp, username, event_type, target_table, target_column, target_value, description )
            VALUES (?,?,?,?,?,?,? )''', ( time.time(), 'bigBird', dcEventTypes['notes'], args.targetTable, args.targetColumn, args.targetValue, args.notes ) )

        conn.commit()

        self.status = 'success'
