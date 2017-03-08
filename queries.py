import sqlite3
# import pandas as pd
#
# pd.set_option('display.width', 240)
# pd.set_option('display.height', 150)
# pd.set_option('display.memory_usage', True)


conn = sqlite3.connect('AHSMap.sqlite')
cur = conn.cursor()



class device:

    def __init__(self,id):

        self.id = id

        #initialize roomproperties
        cur.execute('SELECT * FROM Device WHERE id = ?', (id,))
        row = cur.fetchone()

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

z = device(3)
z.properties()


class cirobj:

    def __init__(self,id):
        self.id = id

        #initialize circuitObject properties
        cur.execute('SELECT * FROM CircuitObject WHERE id = ?', (id,))
        row = cur.fetchone()
        cur.execute('SELECT * FROM Voltage WHERE id = ?',(row[4],))
        voltage = cur.fetchone()
        print(row,voltage)

        self.room_id = row[1]
        self.path = row[2]
        self.voltage = voltage[1]
        self.type = row[5]
        self.description = row[6]
        self.parent = row[2].rsplit('.',maxsplit=1)[0]
        self.root = row[2].split('.',maxsplit=1)[0]

        children = []
        print('my path is', self.path, 'and my children are')
        cur.execute('SELECT path, id FROM CircuitObject WHERE parent = ?', (self.path,))
        COChild = cur.fetchall()
        cur.execute('SELECT description FROM Device WHERE panel_id = ?', (self.id,))
        DevChild = cur.fetchall()
        print(COChild, DevChild)
        for n in COChild:
            print(n)

        for m in DevChild:
            print(m)



z = cirobj(164)


class room:

    def __init__(self,id):
        self.id = id

        #initialize room properties
        cur.execute('SELECT * FROM Room WHERE id = ?', (id,))
        row = cur.fetchone()
        print(row)

        self.oldnum = row[2]
        self.newnum = row[1]
        self.description = row[4]




def old_to_new(old):
    cur.execute('SELECT room_num FROM Room WHERE old_num = ?',(old,))
    r = cur.fetchone()
    return r

#print(old_to_new(1012))

def id_to_new(id):
    cur.execute('SELECT room_num FROM Room WHERE id = ?',(id,))
    r = cur.fetchone()
    return r

#print(id_to_new(3))

def new_to_old(new):
    cur.execute('SELECT old_num FROM Room WHERE room_num = ?', (new,))
    r = cur.fetchone()
    return r

#print(new_to_old('101-11'))


def room_to_closet(room_id):
    #Finds the devices in any room and the closet where you can find their panel(s)
    closets = []
    cur.execute('SELECT room_id FROM CircuitObject WHERE id IN (SELECT panel_id FROM Device WHERE room_id = ?)', (room_id,))
    test = cur.fetchall()
    for tests in test:
        #print("room_id of closet is", tests)
        if tests not in closets:
            closets.append(tests)
    return closets

#print(room_to_closet(3))
# print(room_to_closet(7))
