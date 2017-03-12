import printctl

printctl.off()
import queries
printctl.on()

z = queries.device(3)
z.properties()
