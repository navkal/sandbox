import json

import printctl
printctl.off()
import queries
printctl.on()

dev = queries.device(3)
print( json.dumps( dev.__dict__ ) )
