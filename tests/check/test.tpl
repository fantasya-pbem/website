; --------------------------------
; - fcheck-Template für Unit-Tests
; --------------------------------

^(\/\/)|(komme|kommen|komment|kommenta|kommentar).*$
^;.*$
^@[a-z].+$
^@ +\* +[a-z].+$
^@ +[0-9]+ +[a-z].+$
^@ +[0-9]+/+[0-9]+ +[a-z].+$
^=[0-9]+( +\+[0-9]+)? +[a-z].+$
^\+[0-9]+( +=[0-9]+)? +[a-z].+$

^(locale) +[a-zA-Z0-9]+$
^(region) +.+$

^@?(bo|bot|bots|botsc|botsch|botscha|botschaf|botschaft) +(einheit +)?[a-z0-9]{1,6} +.+$
^@?(bo|bot|bots|botsc|botsch|botscha|botschaf|botschaft) +region +.+$
^@?(bo|bot|bots|botsc|botsch|botscha|botschaf|botschaft) +(burg|gebäude|gebaeude|partei|schiff) +[a-z0-9]{1,6} +.+$

^(einh|einhe|einheit) +[a-z0-9]{1,6}$

^(end|ende)$

^@?(nu|num|numm|numme|nummer|i|id)( +(einheit|gebaeude|gebäude|burg|schiff|partei))? +[a-z0-9]{1,6}$
