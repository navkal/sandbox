import sqlite3

conn = sqlite3.connect('AHSMap.sqlite')
cur = conn.cursor()

class device:
    def __init__(self,id):
        self.id = id

        #initialize roomproperties
        cur.execute('SELECT * FROM Device WHERE id = ?', (id,))
        row = cur.fetchone()
        print("I am a device. This is me:", row)

        self.room_id = row[1]
        self.panel_id = row[2]
        self.description = row[3]
        self.parent = row[5]

        #gets room where device's panel is located
        cur.execute('SELECT room_num, old_num FROM Room WHERE id = (SELECT room_id FROM CircuitObject WHERE id = ?)', (self.panel_id,))
        room = cur.fetchone()

        self.closet_new = room[0]
        self.closet_old = room[1]

    def properties(self):
        print("room_id:", self.room_id)
        print("panel_id:",self.panel_id)
        print("description:", self.description)
        print("parent:",self.parent)
        print("closet_new:", self.closet_new)
        print("closet_old:", self.closet_old)

    def get_main_display(self):
        return {'ID': self.id,
                'Room ID': self.room_id,
                'Panel ID': self.panel_id,
                'Description':self.description,
                'Parent': self.parent,
                'Closet New': self.closet_new,
                'Closet Old': self.closet_old}
# print("DEVICE 754")
# z = device(754)
# display = z.get_main_display()
# print(display)


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
        self.type = row[5]
        self.description = row[6]
        self.parent = row[2].rsplit('.',maxsplit=1)[0]
        self.root = row[2].split('.',maxsplit=1)[0]

        cur.execute('SELECT id, path, description FROM CircuitObject WHERE parent = ?', (self.path,))
        self.children = cur.fetchall()
        cur.execute('SELECT id, room_id, description FROM Device WHERE parent = ?', (self.path,))
        self.devices = cur.fetchall()
        print('my parent is ',self.parent)
        print('my root is ',self.root)
        print('my children are ', self.children)
        print('my devcies are ', self.devices)


    def get_main_display(self):
        return {'ID': self.id,
                'Room ID': self.room_id,
                'Path': self.path,
                'Voltage': self.voltage,
                'Type': self.type,
                'Description': self.description,
                'Parent': self.parent,
                'Root': self.root,
                'Children': self.children}

###########################################
##########GETS ROOT OF CIROBJ#######################
z = cirobj()
display = z.get_main_display()
print(display['Root'])
#################################
# print("CIROBJ 14")
# z = cirobj(14)
# display = z.get_main_display()
# print(display)

# print("CIROBJ 740")
# z = cirobj(720)
# display = z.get_main_display()
# print(display)

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

# z = room(3)
# display = z.get_main_display()
# z.properties()




# def old_to_new(old):
#     cur.execute('SELECT room_num FROM Room WHERE old_num = ?',(old,))
#     r = cur.fetchone()
#     return r

# #print(old_to_new(1012))

# def id_to_new(id):
#     cur.execute('SELECT room_num FROM Room WHERE id = ?',(id,))
#     r = cur.fetchone()
#     return r

# #print(id_to_new(3))

# def new_to_old(new):
#     cur.execute('SELECT old_num FROM Room WHERE room_num = ?', (new,))
#     r = cur.fetchone()
#     return r

# #print(new_to_old('101-11'))


# def room_to_closet(room_id):
#     #Finds the devices in any room and the closet where you can find their panel(s)
#     closets = []
#     cur.execute('SELECT room_id FROM CircuitObject WHERE id IN (SELECT panel_id FROM Device WHERE room_id = ?)', (room_id,))
#     test = cur.fetchall()
#     for tests in test:
#         #print("room_id of closet is", tests)
#         if tests not in closets:
#             closets.append(tests)
#     return closets

#print(room_to_closet(3))
# print(room_to_closet(7))




#
# conn = sqlite3.connect('AHSMap.sqlite')
# cur = conn.cursor()
#
# df = pd.read_csv('devices.csv',encoding='ISO-8859-1',)
#
#
# df = df[['DeviceObj','Circuit','Old Location']]
# print(df)
# s = ('MWSB.5.DHB.1.L42sec2.38.T2C.L2C.35',)
# t = ('4047',)
# cur.execute('SELECT id FROM Room WHERE old_num=?', t)
# print(cur.fetchone())
# cur.execute('SELECT id, description, object_type FROM CircuitObject WHERE path=?', s)
# print(cur.fetchone())
#
#
#
# def get_room_index(room_number):
#     cur.execute('SELECT id FROM CircuitObject WHERE path = ?', (room_number,))
#     index = cur.fetchone()
#     return index[0]
#
#
# for circuit in df.Circuit:
#     z = get_circuit_index(circuit)
#     print(z)


# t = ('1095',)
# cur.execute('SELECT id,room_num,old_num FROM Room WHERE old_num=?', t)
# #print(cur.fetchone())
#
#
# Room = pd.read_sql('SELECT * FROM Room',conn)
#
# #print(Room)
#
#
# devices = pd.read_csv('devices.csv',encoding='ISO-8859-1')
#
# #print(devices)
#
# devices = devices.merge(Room[['old_num','id']],how='left',left_on='Old Location',right_on='old_num')
# print(devices)
#
#
# df = devices.merge(Room[['room_num','id']],how='left',left_on='New Location',right_on='room_num')
# print(df[['Old Location', 'old_num', 'id_x', 'New Location','room_num', 'id_y']])
# device = df[df['id_x'] == df['id_y']]
# print(device)
# device.rename()
# # df['room_id'] = df[['id_x','id_y']].apply(lambda x : x.unique)
# devices = devices[['room_id', 'panel_id', 'description', 'power']]
#
#
# # #
# # device.to_sql('devices',
# #               conn,
# #               if_exists='replace',
# #               dtype={'id':'INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT UNIQUE',
# #                      'room_id':'INTEGER',
# #                      'panel_id':'INTEGER',
# #                      'description':'TEXT',
# #                      'power':'INTEGER'}
# #               )
# # #
# #
