import json

import printctl
printctl.off()
import queries
obj = queries.device(3)
obj = queries.cirobj(164)
printctl.on()
print( json.dumps( obj.__dict__ ) )
