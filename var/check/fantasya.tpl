; RegEx-Template für Fantasya 2 bzw. FCheck2


^(fantasya) [a-z0-9]{1,4} (")[a-zA-Z0-9]+(")$
^(eressea) [a-z0-9]{1,4} (")[a-zA-Z0-9]+(")$
^(partei) [a-z0-9]{1,4} (")[a-zA-Z0-9]+(")$
^(locale) [a-zA-Z0-9]+$
^(region) .+$
^(runde) [0-9]{1,4}$
^(einheit) [a-z0-9]{1,4}$
^(naechster|nächster)$

^@?(mache)[n]? (temp) [a-z0-9]{1,4}([ ]+(\/\/).*)?
^@?(ende)([ ]+(\/\/).*)?

^(\/\/).*
^;.*

^@?((vergesse)[n]?|(vergiss)) ((tarnung)|(kräuterkunde)|(burgenbau)|(straßenbau)|(katapultbedienung)|(unterhaltung)|(armbrustschiessen)|(handel)|(bogenschiessen)|(kraeuterkunden)|(holzfaellen)|(religion)|(steuereintreiben)|(segeln)|(reiten)|(ausdauer)|(taktik)|(handeln)|(drachenreiten)|(waffenbau)|(bogenbau)|(kraeuterkunde)|(monsterkampf)|(armbrustschießen)|(steinbau)|(alchemie)|(rüstungsbau)|(speerkampf)|(kräuterkunden)|(wagenbau)|(pferdedressur)|(ruestungsbau)|(magie)|(strassenbau)|(schiffbau)|(wahrnehmung)|(hiebwaffen)|(bergbau)|(bogenschießen)|(spionage)|(holzfällen))([ ]+(\/\/).*)?
^@?((vergesse)[n]?|(vergiss)) [1-9]{1}[0-9]{0,5} ((tarnung)|(kräuterkunde)|(burgenbau)|(straßenbau)|(katapultbedienung)|(unterhaltung)|(armbrustschiessen)|(handel)|(bogenschiessen)|(kraeuterkunden)|(holzfaellen)|(religion)|(steuereintreiben)|(segeln)|(reiten)|(ausdauer)|(taktik)|(handeln)|(drachenreiten)|(waffenbau)|(bogenbau)|(kraeuterkunde)|(monsterkampf)|(armbrustschießen)|(steinbau)|(alchemie)|(rüstungsbau)|(speerkampf)|(kräuterkunden)|(wagenbau)|(pferdedressur)|(ruestungsbau)|(magie)|(strassenbau)|(schiffbau)|(wahrnehmung)|(hiebwaffen)|(bergbau)|(bogenschießen)|(spionage)|(holzfällen))([ ]+(\/\/).*)?

^@?(helfe)[n]? [a-z0-9]{1,4}( nicht)?([ ]+(\/\/).*)?
^@?(helfe)[n]? [a-z0-9]{1,4} (((kaempfe)|(kaempfen))|((gib)|(gibn))|((resourcen)|(resourcenn))|((treiben)|(treibenn))|((handel)|(handeln))|((unterhalte)|(unterhalten))|((kontaktiere)|(kontaktieren))|((steuern)|(steuernn))|((alles)|(allesn))|(ressourcen))( nicht)?([ ]+(\/\/).*)?

^@?(kaempfe)[n]?(( aggressiv)|( vorne)|( fliehe)|( hinten)|( nicht)|( immun)|( vorn))?([ ]+(\/\/).*)?

^@?(kampfzauber) ((angriff)|(verwirrung)|(verteidigung)) ((nicht)|(kein)|(keiner))([ ]+(\/\/).*)?
^(kampfzauber) ((angriff)|(verwirrung)|(verteidigung)) (")?(erdbeben)(")? [1-9][0-9]?([ ]+(\/\/).*)?
^(kampfzauber) ((angriff)|(verwirrung)|(verteidigung)) (")?(feuerball)(")? [1-9][0-9]?([ ]+(\/\/).*)?
^(kampfzauber) ((angriff)|(verwirrung)|(verteidigung)) (")?(feuerwalze)(")? [1-9][0-9]?([ ]+(\/\/).*)?
^(kampfzauber) ((angriff)|(verwirrung)|(verteidigung)) (")?(kleines erdbeben)(")? [1-9][0-9]?([ ]+(\/\/).*)?
^(kampfzauber) ((angriff)|(verwirrung)|(verteidigung)) (")?(steinschlag)(")? [1-9][0-9]?([ ]+(\/\/).*)?
^(kampfzauber) ((angriff)|(verwirrung)|(verteidigung)) (")?(sturm)(")? [1-9][0-9]?([ ]+(\/\/).*)?

^@?((sammel)|(sammeln)|(sammle))(( keine beute)|( beute nicht)|( nicht beute))([ ]+(\/\/).*)?
^@?((sammel)|(sammeln)|(sammle))(( tragbare beute)|( beute massvoll))([ ]+(\/\/).*)?
^@?((sammel)|(sammeln)|(sammle))(( alle beute)|( beute alles)|( beute))([ ]+(\/\/).*)?

^@?(attackiere)[n]?( [a-z0-9]{1,4})+([ ]+(\/\/).*)?
^@?(attackiere)[n]? (partei)( [a-z0-9]{1,4})+([ ]+(\/\/).*)?
^@?(attackiere)[n]? ((vorne)|(hinten))([ ]+(\/\/).*)?
^@?(attackiere)[n]?( gezielt)( [a-z0-9]{1,4})+([ ]+(\/\/).*)?


^@?(set) (person)(en)? [0-9]+([ ]+(\/\/).*)?
^@?(set) (item)(s)? [a-z]+ [0-9]+([ ]+(\/\/).*)?
^@?(set) ((skill)|(talent)) [a-z]+ [0-9]+([ ]+(\/\/).*)?
^@?(set) ((faction)|(party)|(volk)|(partei)) [a-z0-9]{1,4}([ ]+(\/\/).*)?
^@?(set) ((ship)|(schiff)) [a-z]+([ ]+(\/\/).*)?
^@?(set) ((building)|(gebaeude)) [a-z]+([ ]+(\/\/).*)?

^@?(website) .+([ ]+(\/\/).*)?
^@?(homepage) .+([ ]+(\/\/).*)?

^@?(beschreibe)[n]? ((einheit)|(region)|(gebaeude)|(gebäude)|(burg)|(schiff)|(volk)|(partei)|(insel)|(kontinent)) .+$

^@?(benenne)[n]? ((einheit)|(region)|(gebäude)|(gebaeude)|(burg)|(schiff)|(volk)|(partei)|(insel)|(kontinent)) .+$

^@?(bewache)[n]? (nicht)([ ]+(\/\/).*)?

^@?(ursprung) [-]?[0-9]+ [-]?[0-9]+([ ]+(\/\/).*)?

^@?(zeige)[n]? (zauberbuch)([ ]+(\/\/).*)?
^@?(zeige)[n]? .+

^@?(kontaktiere)[n]?( temp)[a-z0-9]{1,4}([ ]+(\/\/).*)?
^@?(kontaktiere)[n]? [a-z0-9]{1,4}([ ]+(\/\/).*)?
^@?(kontaktiere)[n]?( temp)[a-z0-9]{1,4} (permanent)([ ]+(\/\/).*)?
^@?(kontaktiere)[n]? [a-z0-9]{1,4} (permanent)([ ]+(\/\/).*)?

^@?(sortiere)n? (vor) [a-z0-9]{1,4}([ ]+(\/\/).*)?
^@?(sortiere)n? ((nach)|(hinter)) [a-z0-9]{1,4}([ ]+(\/\/).*)?
^@?(sortiere)n? ((vorn)|(vorne)|(anfang)|(erste)|(erster)|(erstes))([ ]+(\/\/).*)?
^@?(sortiere)n? ((ende)|(hinten)|(letzte)|(letzter)|(letztes))([ ]+(\/\/).*)?

^@?(gib) [a-z0-9]{1,4} (")?((alpaka)|(alpakas)|(amulette der heilung)|(amulett der heilung)|(amuletderheilung)|(amulette des wahren sehens)|(amuletdessehens)|(amulett des wahren sehens)|(armbrueste)|(armbrust)|(balsam)|(bluetengrumpfe)|(bluetengrumpf)|(boegen)|(bogen)|(drachenpanzer)|(einhorn)|(einhörner)|(einhoerner)|(eisen)|(eisenschilde)|(eisenschild)|(elefant)|(elefanten)|(elefantenpanzer)|(flachtupfe)|(flachtupf)|(flugdrache)|(flugdrachen)|(dracheneier)|(drachenei)|(flugdracheneier)|(flugdrachenei)|(gewuerz)|(gewuerze)|(gold)|(greife)|(greif)|(greifenei)|(greifeneier)|(grotenolm)|(grotenolme)|(helmder7winde)|(helme der 7 winde)|(helm der 7 winde)|(helme der sieben winde)|(helm der sieben winde)|(holz)|(holzschilde)|(holzschild)|(juwel)|(juwelen)|(kamel)|(kamele)|(katapult)|(katapulte)|(kettenhemden)|(kettenhemd)|(kriegselefant)|(kriegselefanten)|(kriegshammer)|(kriegsmastodon)|(kriegsmastodons)|(mantel der unverletzlichkeit)|(mäntel der unverletzlichkeit)|(maentel der unverletzlichkeit)|(mantelderunverletzlichkeit)|(mastodons)|(mastodon)|(mastodonten)|(mastodonpanzer)|(säcke myrrhe)|(sack myrrhe)|(myrrhe)|(myhrre)|(myrre)|(oele)|(oel)|(pegasus)|(pegasi)|(pelz)|(pelze)|(pferd)|(pferde)|(plattenpanzer)|(ring der kraft)|(ringderkraft)|(ringe der kraft)|(ringderunsichtbarkeit)|(ring der unsichtbarkeit)|(ringe der unsichtbarkeit)|(runenschwert)|(runenschwerter)|(schildsteine)|(schildstein)|(schnupfniese)|(schnupfnies)|(schwert)|(schwerter)|(ballen feinster seide)|(seide)|(silber)|(silberkiste)|(speere)|(speer)|(stein)|(steine)|(streitaxt)|(streitaexte)|(sumpfkraeuter)|(sumpfkraut)|(trockenwurz)|(trockenwurze)|(wagen)|(weihrauch)|(wirzblatt)|(wirzblaetter)|(zotte)|(zotten))(")?([ ]+(\/\/).*)?
^@?(liefere) [a-z0-9]{1,4} (")?((alpaka)|(alpakas)|(amulette der heilung)|(amulett der heilung)|(amuletderheilung)|(amulette des wahren sehens)|(amuletdessehens)|(amulett des wahren sehens)|(armbrueste)|(armbrust)|(balsam)|(bluetengrumpfe)|(bluetengrumpf)|(boegen)|(bogen)|(drachenpanzer)|(einhorn)|(einhörner)|(einhoerner)|(eisen)|(eisenschilde)|(eisenschild)|(elefant)|(elefanten)|(elefantenpanzer)|(flachtupfe)|(flachtupf)|(flugdrache)|(flugdrachen)|(dracheneier)|(drachenei)|(flugdracheneier)|(flugdrachenei)|(gewuerz)|(gewuerze)|(gold)|(greife)|(greif)|(greifenei)|(greifeneier)|(grotenolm)|(grotenolme)|(helmder7winde)|(helme der 7 winde)|(helm der 7 winde)|(helme der sieben winde)|(helm der sieben winde)|(holz)|(holzschilde)|(holzschild)|(juwel)|(juwelen)|(kamel)|(kamele)|(katapult)|(katapulte)|(kettenhemden)|(kettenhemd)|(kriegselefant)|(kriegselefanten)|(kriegshammer)|(kriegsmastodon)|(kriegsmastodons)|(mantel der unverletzlichkeit)|(mäntel der unverletzlichkeit)|(maentel der unverletzlichkeit)|(mantelderunverletzlichkeit)|(mastodons)|(mastodon)|(mastodonten)|(mastodonpanzer)|(säcke myrrhe)|(sack myrrhe)|(myrrhe)|(myhrre)|(myrre)|(oele)|(oel)|(pegasus)|(pegasi)|(pelz)|(pelze)|(pferd)|(pferde)|(plattenpanzer)|(ring der kraft)|(ringderkraft)|(ringe der kraft)|(ringderunsichtbarkeit)|(ring der unsichtbarkeit)|(ringe der unsichtbarkeit)|(runenschwert)|(runenschwerter)|(schildsteine)|(schildstein)|(schnupfniese)|(schnupfnies)|(schwert)|(schwerter)|(ballen feinster seide)|(seide)|(silber)|(silberkiste)|(speere)|(speer)|(stein)|(steine)|(streitaxt)|(streitaexte)|(sumpfkraeuter)|(sumpfkraut)|(trockenwurz)|(trockenwurze)|(wagen)|(weihrauch)|(wirzblatt)|(wirzblaetter)|(zotte)|(zotten))(")?([ ]+(\/\/).*)?
^@?(gib) (temp) [a-z0-9]{1,4} (")?((alpaka)|(alpakas)|(amulette der heilung)|(amulett der heilung)|(amuletderheilung)|(amulette des wahren sehens)|(amuletdessehens)|(amulett des wahren sehens)|(armbrueste)|(armbrust)|(balsam)|(bluetengrumpfe)|(bluetengrumpf)|(boegen)|(bogen)|(drachenpanzer)|(einhorn)|(einhörner)|(einhoerner)|(eisen)|(eisenschilde)|(eisenschild)|(elefant)|(elefanten)|(elefantenpanzer)|(flachtupfe)|(flachtupf)|(flugdrache)|(flugdrachen)|(dracheneier)|(drachenei)|(flugdracheneier)|(flugdrachenei)|(gewuerz)|(gewuerze)|(gold)|(greife)|(greif)|(greifenei)|(greifeneier)|(grotenolm)|(grotenolme)|(helmder7winde)|(helme der 7 winde)|(helm der 7 winde)|(helme der sieben winde)|(helm der sieben winde)|(holz)|(holzschilde)|(holzschild)|(juwel)|(juwelen)|(kamel)|(kamele)|(katapult)|(katapulte)|(kettenhemden)|(kettenhemd)|(kriegselefant)|(kriegselefanten)|(kriegshammer)|(kriegsmastodon)|(kriegsmastodons)|(mantel der unverletzlichkeit)|(mäntel der unverletzlichkeit)|(maentel der unverletzlichkeit)|(mantelderunverletzlichkeit)|(mastodons)|(mastodon)|(mastodonten)|(mastodonpanzer)|(säcke myrrhe)|(sack myrrhe)|(myrrhe)|(myhrre)|(myrre)|(oele)|(oel)|(pegasus)|(pegasi)|(pelz)|(pelze)|(pferd)|(pferde)|(plattenpanzer)|(ring der kraft)|(ringderkraft)|(ringe der kraft)|(ringderunsichtbarkeit)|(ring der unsichtbarkeit)|(ringe der unsichtbarkeit)|(runenschwert)|(runenschwerter)|(schildsteine)|(schildstein)|(schnupfniese)|(schnupfnies)|(schwert)|(schwerter)|(ballen feinster seide)|(seide)|(silber)|(silberkiste)|(speere)|(speer)|(stein)|(steine)|(streitaxt)|(streitaexte)|(sumpfkraeuter)|(sumpfkraut)|(trockenwurz)|(trockenwurze)|(wagen)|(weihrauch)|(wirzblatt)|(wirzblaetter)|(zotte)|(zotten))(")?([ ]+(\/\/).*)?
^@?(liefere) (temp) [a-z0-9]{1,4} (")?((alpaka)|(alpakas)|(amulette der heilung)|(amulett der heilung)|(amuletderheilung)|(amulette des wahren sehens)|(amuletdessehens)|(amulett des wahren sehens)|(armbrueste)|(armbrust)|(balsam)|(bluetengrumpfe)|(bluetengrumpf)|(boegen)|(bogen)|(drachenpanzer)|(einhorn)|(einhörner)|(einhoerner)|(eisen)|(eisenschilde)|(eisenschild)|(elefant)|(elefanten)|(elefantenpanzer)|(flachtupfe)|(flachtupf)|(flugdrache)|(flugdrachen)|(dracheneier)|(drachenei)|(flugdracheneier)|(flugdrachenei)|(gewuerz)|(gewuerze)|(gold)|(greife)|(greif)|(greifenei)|(greifeneier)|(grotenolm)|(grotenolme)|(helmder7winde)|(helme der 7 winde)|(helm der 7 winde)|(helme der sieben winde)|(helm der sieben winde)|(holz)|(holzschilde)|(holzschild)|(juwel)|(juwelen)|(kamel)|(kamele)|(katapult)|(katapulte)|(kettenhemden)|(kettenhemd)|(kriegselefant)|(kriegselefanten)|(kriegshammer)|(kriegsmastodon)|(kriegsmastodons)|(mantel der unverletzlichkeit)|(mäntel der unverletzlichkeit)|(maentel der unverletzlichkeit)|(mantelderunverletzlichkeit)|(mastodons)|(mastodon)|(mastodonten)|(mastodonpanzer)|(säcke myrrhe)|(sack myrrhe)|(myrrhe)|(myhrre)|(myrre)|(oele)|(oel)|(pegasus)|(pegasi)|(pelz)|(pelze)|(pferd)|(pferde)|(plattenpanzer)|(ring der kraft)|(ringderkraft)|(ringe der kraft)|(ringderunsichtbarkeit)|(ring der unsichtbarkeit)|(ringe der unsichtbarkeit)|(runenschwert)|(runenschwerter)|(schildsteine)|(schildstein)|(schnupfniese)|(schnupfnies)|(schwert)|(schwerter)|(ballen feinster seide)|(seide)|(silber)|(silberkiste)|(speere)|(speer)|(stein)|(steine)|(streitaxt)|(streitaexte)|(sumpfkraeuter)|(sumpfkraut)|(trockenwurz)|(trockenwurze)|(wagen)|(weihrauch)|(wirzblatt)|(wirzblaetter)|(zotte)|(zotten))(")?([ ]+(\/\/).*)?
^@?(gib) [a-z0-9]{1,4} [0-9]+ (")?((alpaka)|(alpakas)|(amulette der heilung)|(amulett der heilung)|(amuletderheilung)|(amulette des wahren sehens)|(amuletdessehens)|(amulett des wahren sehens)|(armbrueste)|(armbrust)|(balsam)|(bluetengrumpfe)|(bluetengrumpf)|(boegen)|(bogen)|(drachenpanzer)|(einhorn)|(einhörner)|(einhoerner)|(eisen)|(eisenschilde)|(eisenschild)|(elefant)|(elefanten)|(elefantenpanzer)|(flachtupfe)|(flachtupf)|(flugdrache)|(flugdrachen)|(dracheneier)|(drachenei)|(flugdracheneier)|(flugdrachenei)|(gewuerz)|(gewuerze)|(gold)|(greife)|(greif)|(greifenei)|(greifeneier)|(grotenolm)|(grotenolme)|(helmder7winde)|(helme der 7 winde)|(helm der 7 winde)|(helme der sieben winde)|(helm der sieben winde)|(holz)|(holzschilde)|(holzschild)|(juwel)|(juwelen)|(kamel)|(kamele)|(katapult)|(katapulte)|(kettenhemden)|(kettenhemd)|(kriegselefant)|(kriegselefanten)|(kriegshammer)|(kriegsmastodon)|(kriegsmastodons)|(mantel der unverletzlichkeit)|(mäntel der unverletzlichkeit)|(maentel der unverletzlichkeit)|(mantelderunverletzlichkeit)|(mastodons)|(mastodon)|(mastodonten)|(mastodonpanzer)|(säcke myrrhe)|(sack myrrhe)|(myrrhe)|(myhrre)|(myrre)|(oele)|(oel)|(pegasus)|(pegasi)|(pelz)|(pelze)|(pferd)|(pferde)|(plattenpanzer)|(ring der kraft)|(ringderkraft)|(ringe der kraft)|(ringderunsichtbarkeit)|(ring der unsichtbarkeit)|(ringe der unsichtbarkeit)|(runenschwert)|(runenschwerter)|(schildsteine)|(schildstein)|(schnupfniese)|(schnupfnies)|(schwert)|(schwerter)|(ballen feinster seide)|(seide)|(silber)|(silberkiste)|(speere)|(speer)|(stein)|(steine)|(streitaxt)|(streitaexte)|(sumpfkraeuter)|(sumpfkraut)|(trockenwurz)|(trockenwurze)|(wagen)|(weihrauch)|(wirzblatt)|(wirzblaetter)|(zotte)|(zotten))(")?( permanent)?([ ]+(\/\/).*)?
^@?(liefere) [a-z0-9]{1,4} [0-9]+ (")?((alpaka)|(alpakas)|(amulette der heilung)|(amulett der heilung)|(amuletderheilung)|(amulette des wahren sehens)|(amuletdessehens)|(amulett des wahren sehens)|(armbrueste)|(armbrust)|(balsam)|(bluetengrumpfe)|(bluetengrumpf)|(boegen)|(bogen)|(drachenpanzer)|(einhorn)|(einhörner)|(einhoerner)|(eisen)|(eisenschilde)|(eisenschild)|(elefant)|(elefanten)|(elefantenpanzer)|(flachtupfe)|(flachtupf)|(flugdrache)|(flugdrachen)|(dracheneier)|(drachenei)|(flugdracheneier)|(flugdrachenei)|(gewuerz)|(gewuerze)|(gold)|(greife)|(greif)|(greifenei)|(greifeneier)|(grotenolm)|(grotenolme)|(helmder7winde)|(helme der 7 winde)|(helm der 7 winde)|(helme der sieben winde)|(helm der sieben winde)|(holz)|(holzschilde)|(holzschild)|(juwel)|(juwelen)|(kamel)|(kamele)|(katapult)|(katapulte)|(kettenhemden)|(kettenhemd)|(kriegselefant)|(kriegselefanten)|(kriegshammer)|(kriegsmastodon)|(kriegsmastodons)|(mantel der unverletzlichkeit)|(mäntel der unverletzlichkeit)|(maentel der unverletzlichkeit)|(mantelderunverletzlichkeit)|(mastodons)|(mastodon)|(mastodonten)|(mastodonpanzer)|(säcke myrrhe)|(sack myrrhe)|(myrrhe)|(myhrre)|(myrre)|(oele)|(oel)|(pegasus)|(pegasi)|(pelz)|(pelze)|(pferd)|(pferde)|(plattenpanzer)|(ring der kraft)|(ringderkraft)|(ringe der kraft)|(ringderunsichtbarkeit)|(ring der unsichtbarkeit)|(ringe der unsichtbarkeit)|(runenschwert)|(runenschwerter)|(schildsteine)|(schildstein)|(schnupfniese)|(schnupfnies)|(schwert)|(schwerter)|(ballen feinster seide)|(seide)|(silber)|(silberkiste)|(speere)|(speer)|(stein)|(steine)|(streitaxt)|(streitaexte)|(sumpfkraeuter)|(sumpfkraut)|(trockenwurz)|(trockenwurze)|(wagen)|(weihrauch)|(wirzblatt)|(wirzblaetter)|(zotte)|(zotten))(")?([ ]+(\/\/).*)?
^@?(gib) (temp) [a-z0-9]{1,4} [0-9]+ (")?((alpaka)|(alpakas)|(amulette der heilung)|(amulett der heilung)|(amuletderheilung)|(amulette des wahren sehens)|(amuletdessehens)|(amulett des wahren sehens)|(armbrueste)|(armbrust)|(balsam)|(bluetengrumpfe)|(bluetengrumpf)|(boegen)|(bogen)|(drachenpanzer)|(einhorn)|(einhörner)|(einhoerner)|(eisen)|(eisenschilde)|(eisenschild)|(elefant)|(elefanten)|(elefantenpanzer)|(flachtupfe)|(flachtupf)|(flugdrache)|(flugdrachen)|(dracheneier)|(drachenei)|(flugdracheneier)|(flugdrachenei)|(gewuerz)|(gewuerze)|(gold)|(greife)|(greif)|(greifenei)|(greifeneier)|(grotenolm)|(grotenolme)|(helmder7winde)|(helme der 7 winde)|(helm der 7 winde)|(helme der sieben winde)|(helm der sieben winde)|(holz)|(holzschilde)|(holzschild)|(juwel)|(juwelen)|(kamel)|(kamele)|(katapult)|(katapulte)|(kettenhemden)|(kettenhemd)|(kriegselefant)|(kriegselefanten)|(kriegshammer)|(kriegsmastodon)|(kriegsmastodons)|(mantel der unverletzlichkeit)|(mäntel der unverletzlichkeit)|(maentel der unverletzlichkeit)|(mantelderunverletzlichkeit)|(mastodons)|(mastodon)|(mastodonten)|(mastodonpanzer)|(säcke myrrhe)|(sack myrrhe)|(myrrhe)|(myhrre)|(myrre)|(oele)|(oel)|(pegasus)|(pegasi)|(pelz)|(pelze)|(pferd)|(pferde)|(plattenpanzer)|(ring der kraft)|(ringderkraft)|(ringe der kraft)|(ringderunsichtbarkeit)|(ring der unsichtbarkeit)|(ringe der unsichtbarkeit)|(runenschwert)|(runenschwerter)|(schildsteine)|(schildstein)|(schnupfniese)|(schnupfnies)|(schwert)|(schwerter)|(ballen feinster seide)|(seide)|(silber)|(silberkiste)|(speere)|(speer)|(stein)|(steine)|(streitaxt)|(streitaexte)|(sumpfkraeuter)|(sumpfkraut)|(trockenwurz)|(trockenwurze)|(wagen)|(weihrauch)|(wirzblatt)|(wirzblaetter)|(zotte)|(zotten))(")?( permanent)?([ ]+(\/\/).*)?
^@?(liefere) (temp) [a-z0-9]{1,4} [0-9]+ (")?((alpaka)|(alpakas)|(amulette der heilung)|(amulett der heilung)|(amuletderheilung)|(amulette des wahren sehens)|(amuletdessehens)|(amulett des wahren sehens)|(armbrueste)|(armbrust)|(balsam)|(bluetengrumpfe)|(bluetengrumpf)|(boegen)|(bogen)|(drachenpanzer)|(einhorn)|(einhörner)|(einhoerner)|(eisen)|(eisenschilde)|(eisenschild)|(elefant)|(elefanten)|(elefantenpanzer)|(flachtupfe)|(flachtupf)|(flugdrache)|(flugdrachen)|(dracheneier)|(drachenei)|(flugdracheneier)|(flugdrachenei)|(gewuerz)|(gewuerze)|(gold)|(greife)|(greif)|(greifenei)|(greifeneier)|(grotenolm)|(grotenolme)|(helmder7winde)|(helme der 7 winde)|(helm der 7 winde)|(helme der sieben winde)|(helm der sieben winde)|(holz)|(holzschilde)|(holzschild)|(juwel)|(juwelen)|(kamel)|(kamele)|(katapult)|(katapulte)|(kettenhemden)|(kettenhemd)|(kriegselefant)|(kriegselefanten)|(kriegshammer)|(kriegsmastodon)|(kriegsmastodons)|(mantel der unverletzlichkeit)|(mäntel der unverletzlichkeit)|(maentel der unverletzlichkeit)|(mantelderunverletzlichkeit)|(mastodons)|(mastodon)|(mastodonten)|(mastodonpanzer)|(säcke myrrhe)|(sack myrrhe)|(myrrhe)|(myhrre)|(myrre)|(oele)|(oel)|(pegasus)|(pegasi)|(pelz)|(pelze)|(pferd)|(pferde)|(plattenpanzer)|(ring der kraft)|(ringderkraft)|(ringe der kraft)|(ringderunsichtbarkeit)|(ring der unsichtbarkeit)|(ringe der unsichtbarkeit)|(runenschwert)|(runenschwerter)|(schildsteine)|(schildstein)|(schnupfniese)|(schnupfnies)|(schwert)|(schwerter)|(ballen feinster seide)|(seide)|(silber)|(silberkiste)|(speere)|(speer)|(stein)|(steine)|(streitaxt)|(streitaexte)|(sumpfkraeuter)|(sumpfkraut)|(trockenwurz)|(trockenwurze)|(wagen)|(weihrauch)|(wirzblatt)|(wirzblaetter)|(zotte)|(zotten))(")?([ ]+(\/\/).*)?
^@?(gib) (bauern) ((alpaka)|(alpakas)|(einhorn)|(einhörner)|(einhoerner)|(elefant)|(elefanten)|(flugdrache)|(flugdrachen)|(greife)|(greif)|(kamel)|(kamele)|(mastodons)|(mastodon)|(mastodonten)|(pegasus)|(pegasi)|(pferd)|(pferde)|(zotte)|(zotten))([ ]+(\/\/).*)?
^@?(liefere) (bauern) ((alpaka)|(alpakas)|(einhorn)|(einhörner)|(einhoerner)|(elefant)|(elefanten)|(flugdrache)|(flugdrachen)|(greife)|(greif)|(kamel)|(kamele)|(mastodons)|(mastodon)|(mastodonten)|(pegasus)|(pegasi)|(pferd)|(pferde)|(zotte)|(zotten))([ ]+(\/\/).*)?
^@?(gib) (bauern) [0-9]+ ((alpaka)|(alpakas)|(einhorn)|(einhörner)|(einhoerner)|(elefant)|(elefanten)|(flugdrache)|(flugdrachen)|(greife)|(greif)|(kamel)|(kamele)|(mastodons)|(mastodon)|(mastodonten)|(pegasus)|(pegasi)|(pferd)|(pferde)|(zotte)|(zotten))([ ]+(\/\/).*)?
^@?(liefere) (bauern) [0-9]+ ((alpaka)|(alpakas)|(einhorn)|(einhörner)|(einhoerner)|(elefant)|(elefanten)|(flugdrache)|(flugdrachen)|(greife)|(greif)|(kamel)|(kamele)|(mastodons)|(mastodon)|(mastodonten)|(pegasus)|(pegasi)|(pferd)|(pferde)|(zotte)|(zotten))([ ]+(\/\/).*)?
^@?(gib) [a-z0-9]{1,4} (alles)([ ]+(\/\/).*)?
^@?(liefere) [a-z0-9]{1,4} (alles)([ ]+(\/\/).*)?
^@?(gib) (temp) [a-z0-9]{1,4} (alles)([ ]+(\/\/).*)?
^@?(liefere) (temp) [a-z0-9]{1,4} (alles)([ ]+(\/\/).*)?
^@?(gib) [a-z0-9]{1,4} [0-9]+ (person)(en)?([ ]+(\/\/).*)?
^@?(liefere) [a-z0-9]{1,4} [0-9]+ (person)(en)?([ ]+(\/\/).*)?
^@?(gib) (temp) [a-z0-9]{1,4} [0-9]+ (person)(en)?([ ]+(\/\/).*)?
^@?(liefere) (temp) [a-z0-9]{1,4} [0-9]+ (person)(en)?([ ]+(\/\/).*)?
^@?(gib) (bauern) (person)(en)?([ ]+(\/\/).*)?
^@?(liefere) (bauern) (person)(en)?([ ]+(\/\/).*)?
^@?(gib) (bauern) [0-9]+ (person)(en)?([ ]+(\/\/).*)?
^@?(liefere) (bauern) [0-9]+ (person)(en)?([ ]+(\/\/).*)?
^@?(gib) [a-z0-9]{1,4} einheit([ ]+(\/\/).*)?
^@?(liefere) [a-z0-9]{1,4} einheit([ ]+(\/\/).*)?
^@?(gib) (bauern) einheit([ ]+(\/\/).*)?
^@?(liefere) (bauern) einheit([ ]+(\/\/).*)?
^@?(gib) [a-z0-9]{1,4} (zauberbuch)([ ]+(\/\/).*)?
^@?(liefere) [a-z0-9]{1,4} (zauberbuch)([ ]+(\/\/).*)?
^@?(gib) (temp) [a-z0-9]{1,4} (zauberbuch)([ ]+(\/\/).*)?
^@?(liefere) (temp) [a-z0-9]{1,4} (zauberbuch)([ ]+(\/\/).*)?
^@?(gib) [a-z0-9]{1,4}( zauberbuch)? (")?((erdbeben)|(fernsicht)|(feuerball)|(feuerwalze)|(guter wind)|(hain der 1000 eichen)|(hammer der götter)|(klauen der tiefe)|(kleines erdbeben)|(luftreise)|(meister der platten)|(meister der resourcen)|(meister der schmiede)|(meister der wagen)|(meister des schiffs)|(provokation der titanen)|(segen der göttin)|(steinschlag)|(sturm)|(voodoo))(")?([ ]+(\/\/).*)?
^@?(liefere) [a-z0-9]{1,4}( zauberbuch)? (")?((erdbeben)|(fernsicht)|(feuerball)|(feuerwalze)|(guter wind)|(hain der 1000 eichen)|(hammer der götter)|(klauen der tiefe)|(kleines erdbeben)|(luftreise)|(meister der platten)|(meister der resourcen)|(meister der schmiede)|(meister der wagen)|(meister des schiffs)|(provokation der titanen)|(segen der göttin)|(steinschlag)|(sturm)|(voodoo))(")?([ ]+(\/\/).*)?
^@?(gib) (temp) [a-z0-9]{1,4}( zauberbuch)? (")?((erdbeben)|(fernsicht)|(feuerball)|(feuerwalze)|(guter wind)|(hain der 1000 eichen)|(hammer der götter)|(klauen der tiefe)|(kleines erdbeben)|(luftreise)|(meister der platten)|(meister der resourcen)|(meister der schmiede)|(meister der wagen)|(meister des schiffs)|(provokation der titanen)|(segen der göttin)|(steinschlag)|(sturm)|(voodoo))(")?([ ]+(\/\/).*)?
^@?(liefere) (temp) [a-z0-9]{1,4}( zauberbuch)? (")?((erdbeben)|(fernsicht)|(feuerball)|(feuerwalze)|(guter wind)|(hain der 1000 eichen)|(hammer der götter)|(klauen der tiefe)|(kleines erdbeben)|(luftreise)|(meister der platten)|(meister der resourcen)|(meister der schmiede)|(meister der wagen)|(meister des schiffs)|(provokation der titanen)|(segen der göttin)|(steinschlag)|(sturm)|(voodoo))(")?([ ]+(\/\/).*)?

^@?(nummer) ((einheit)|(gebaeude)|(gebäude)|(burg)|(schiff)|(volk)|(partei)) [a-z0-9]{1,4}([ ]+(\/\/).*)?

^@?(tarne)[n]? (einheit)([ ]+(\/\/).*)?
^@?(tarne)[n]? (einheit) (nicht)([ ]+(\/\/).*)?
^@?(tarne)[n]? ((volk)|(partei)) [a-z0-9]{1,4}([ ]+(\/\/).*)?
^@?(tarne)[n]? ((volk)|(partei)) (nicht)([ ]+(\/\/).*)?
^@?(tarne)[n]? ((volk)|(partei))([ ]+(\/\/).*)?
^@?(tarne)[n]? (rasse) ((aquaner)|(dragonfly)|(echse)|(elf)|(goblin)|(greif)|(halbling)|(hoellenhund)|(kobold)|(krake)|(mensch)|(ork)|(puschkin)|(troll)|(zombie)|(zwerg))([ ]+(\/\/).*)?
^@?(tarne)[n]? (rasse) (nicht)([ ]+(\/\/).*)?
^@?(tarne)[n]? (nicht)([ ]+(\/\/).*)?

^@?((praefix)|(präfix)|(prefix)) (")?(.*)(")?([ ]+(\/\/).*)?
^@?((praefix)|(präfix)|(prefix))[ ]*((\/\/).*)?

^@?(steuer)(n)? ((100)|([0-9]{1,2}))([ ]+(\/\/).*)?
^@?(steuer)(n)? ((100)|([0-9]{1,2})) [a-z0-9]{1,4}([ ]+(\/\/).*)?

^@?(rekrutiere)[n]? [1-9][0-9]*([ ]+(\/\/).*)?

^(stirb) (")?.+(")?([ ]+(\/\/).*)?

^@?(betrete)[n]? ((gebaeude)|(gebäude)|(burg)) [a-z0-9]{1,4}([ ]+(\/\/).*)?
^@?(betrete)[n]? (schiff) [a-z0-9]{1,4}([ ]+(\/\/).*)?
^@?(betrete)[n]? ((hoehle)|(höhle)) [a-z0-9]{1,4}([ ]+(\/\/).*)?

^@?(gib) [a-z0-9]{1,4} (kommando)([ ]+(\/\/).*)?
^@?(gib) (temp) [a-z0-9]{1,4} (kommando)([ ]+(\/\/).*)?

^@?(verlasse)[n]?(( schiff)|( gebaeude)|( gebäude))?([ ]+(\/\/).*)?

^(zauber)[en]? (((")?(erdbeben)(")? [1-9][0-9]?)|((")?(fernsicht)(")? [-+]?[0-9]+ [-+]?[0-9])|((")?(feuerball)(")? [1-9][0-9]?)|((")?(feuerwalze)(")? [1-9][0-9]?)|("?(guter wind)"? [a-z0-9]{1,4}( [0-9]+)?)|("?(hain der 1000 eichen)"?( [0-9]+))|((")?(hammer der götter)(")?)|((")?(hammer der goetter)(")?)|(("klauen der tiefe") [0-9]+)|((")?(kleines erdbeben)(")? [1-9][0-9]?)|("?(luftreise)"? [-+]?[0-9]+ [-+]?[0-9]+)|("?(meister der platten)"? [a-z0-9]{1,4}( [0-9]+)?)|("?(meister der resourcen)"? [a-z0-9]{1,4}( [0-9]+)?)|("?(meister der schmiede)"? [a-z0-9]{1,4}( [0-9]+)?)|("?(meister der wagen)"? [a-z0-9]{1,4}( [0-9]+)?)|("?(meister des schiffs)"? [a-z0-9]{1,4}( [0-9]+)?)|((")?(provokation der titanen)(")?)|((")?(segen der göttin)(")?)|((")?(segen der goettin)(")?)|((")?(steinschlag)(")? [1-9][0-9]?)|((")?(sturm)(")? [1-9][0-9]?)|(("voodoo") [a-z0-9]{1,4} (".*")))([ ]+(\/\/).*)?

^(zerstoere)[n]?([ ]+(\/\/).*)?
^(zerstoere)[n]? (strasse) (nw|no|o|so|sw|w)([ ]+(\/\/).*)?

^@?(spioniere)[n]? [a-z0-9]{1,4}([ ]+(\/\/).*)?

^(lehre)[n]? .+([ ]+(\/\/).*)?

^(lerne)[n]? ((alchemie)|(armbrustschießen)|(armbrustschiessen)|(ausdauer)|(bergbau)|(bogenbau)|(bogenschießen)|(bogenschiessen)|(burgenbau)|(drachenreiten)|(handeln)|(handel)|(hiebwaffen)|(holzfaellen)|(holzfällen)|(katapultbedienung)|(kräuterkunde)|(kräuterkunden)|(kraeuterkunde)|(kraeuterkunden)|(magie)|(monsterkampf)|(pferdedressur)|(reiten)|(religion)|(ruestungsbau)|(rüstungsbau)|(schiffbau)|(segeln)|(speerkampf)|(spionage)|(steinbau)|(steuereintreiben)|(strassenbau)|(straßenbau)|(taktik)|(tarnung)|(unterhaltung)|(waffenbau)|(wagenbau)|(wahrnehmung))([ ]+(\/\/).*)?
^(lerne)[n]? ((alchemie)|(armbrustschießen)|(armbrustschiessen)|(ausdauer)|(bergbau)|(bogenbau)|(bogenschießen)|(bogenschiessen)|(burgenbau)|(drachenreiten)|(handeln)|(handel)|(hiebwaffen)|(holzfaellen)|(holzfällen)|(katapultbedienung)|(kräuterkunde)|(kräuterkunden)|(kraeuterkunde)|(kraeuterkunden)|(magie)|(monsterkampf)|(pferdedressur)|(reiten)|(religion)|(ruestungsbau)|(rüstungsbau)|(schiffbau)|(segeln)|(speerkampf)|(spionage)|(steinbau)|(steuereintreiben)|(strassenbau)|(straßenbau)|(taktik)|(tarnung)|(unterhaltung)|(waffenbau)|(wagenbau)|(wahrnehmung)) (t|tw)?[1-9]{1}[0-9]{0,2}([ ]+(\/\/).*)?

^(belager)[en]? [a-z0-9]{1,4}([ ]+(\/\/).*)?

^(mache)[n]? ((alpaka)|(alpakas)|(amulette der heilung)|(amulett der heilung)|(amuletderheilung)|(amulette des wahren sehens)|(amuletdessehens)|(amulett des wahren sehens)|(armbrueste)|(armbrust)|(balsam)|(bluetengrumpfe)|(bluetengrumpf)|(boegen)|(bogen)|(drachenpanzer)|(einhorn)|(einhörner)|(einhoerner)|(eisen)|(eisenschilde)|(eisenschild)|(elefant)|(elefanten)|(elefantenpanzer)|(flachtupfe)|(flachtupf)|(flugdrache)|(flugdrachen)|(dracheneier)|(drachenei)|(flugdracheneier)|(flugdrachenei)|(gewuerz)|(gewuerze)|(gold)|(greife)|(greif)|(greifenei)|(greifeneier)|(grotenolm)|(grotenolme)|(helmder7winde)|(helme der 7 winde)|(helm der 7 winde)|(helme der sieben winde)|(helm der sieben winde)|(holz)|(holzschilde)|(holzschild)|(juwel)|(juwelen)|(kamel)|(kamele)|(katapult)|(katapulte)|(kettenhemden)|(kettenhemd)|(kriegselefant)|(kriegselefanten)|(kriegshammer)|(kriegsmastodon)|(kriegsmastodons)|(mantel der unverletzlichkeit)|(mäntel der unverletzlichkeit)|(maentel der unverletzlichkeit)|(mantelderunverletzlichkeit)|(mastodons)|(mastodon)|(mastodonten)|(mastodonpanzer)|(säcke myrrhe)|(sack myrrhe)|(myrrhe)|(myhrre)|(myrre)|(oele)|(oel)|(pegasus)|(pegasi)|(pelz)|(pelze)|(pferd)|(pferde)|(plattenpanzer)|(ring der kraft)|(ringderkraft)|(ringe der kraft)|(ringderunsichtbarkeit)|(ring der unsichtbarkeit)|(ringe der unsichtbarkeit)|(runenschwert)|(runenschwerter)|(schildsteine)|(schildstein)|(schnupfniese)|(schnupfnies)|(schwert)|(schwerter)|(ballen feinster seide)|(seide)|(silber)|(silberkiste)|(speere)|(speer)|(stein)|(steine)|(streitaxt)|(streitaexte)|(sumpfkraeuter)|(sumpfkraut)|(trockenwurz)|(trockenwurze)|(wagen)|(weihrauch)|(wirzblatt)|(wirzblaetter)|(zotte)|(zotten))([ ]+(\/\/).*)?
^(mache)[n]? ([0-9]+) ((alpaka)|(alpakas)|(amulette der heilung)|(amulett der heilung)|(amuletderheilung)|(amulette des wahren sehens)|(amuletdessehens)|(amulett des wahren sehens)|(armbrueste)|(armbrust)|(balsam)|(bluetengrumpfe)|(bluetengrumpf)|(boegen)|(bogen)|(drachenpanzer)|(einhorn)|(einhörner)|(einhoerner)|(eisen)|(eisenschilde)|(eisenschild)|(elefant)|(elefanten)|(elefantenpanzer)|(flachtupfe)|(flachtupf)|(flugdrache)|(flugdrachen)|(dracheneier)|(drachenei)|(flugdracheneier)|(flugdrachenei)|(gewuerz)|(gewuerze)|(gold)|(greife)|(greif)|(greifenei)|(greifeneier)|(grotenolm)|(grotenolme)|(helmder7winde)|(helme der 7 winde)|(helm der 7 winde)|(helme der sieben winde)|(helm der sieben winde)|(holz)|(holzschilde)|(holzschild)|(juwel)|(juwelen)|(kamel)|(kamele)|(katapult)|(katapulte)|(kettenhemden)|(kettenhemd)|(kriegselefant)|(kriegselefanten)|(kriegshammer)|(kriegsmastodon)|(kriegsmastodons)|(mantel der unverletzlichkeit)|(mäntel der unverletzlichkeit)|(maentel der unverletzlichkeit)|(mantelderunverletzlichkeit)|(mastodons)|(mastodon)|(mastodonten)|(mastodonpanzer)|(säcke myrrhe)|(sack myrrhe)|(myrrhe)|(myhrre)|(myrre)|(oele)|(oel)|(pegasus)|(pegasi)|(pelz)|(pelze)|(pferd)|(pferde)|(plattenpanzer)|(ring der kraft)|(ringderkraft)|(ringe der kraft)|(ringderunsichtbarkeit)|(ring der unsichtbarkeit)|(ringe der unsichtbarkeit)|(runenschwert)|(runenschwerter)|(schildsteine)|(schildstein)|(schnupfniese)|(schnupfnies)|(schwert)|(schwerter)|(ballen feinster seide)|(seide)|(silber)|(silberkiste)|(speere)|(speer)|(stein)|(steine)|(streitaxt)|(streitaexte)|(sumpfkraeuter)|(sumpfkraut)|(trockenwurz)|(trockenwurze)|(wagen)|(weihrauch)|(wirzblatt)|(wirzblaetter)|(zotte)|(zotten))([ ]+(\/\/).*)?
^(mache)[n]? (strasse) (nw|no|o|so|sw|w|nordwesten|nordosten|osten|suedosten|suedwesten|westen)([ ]+(\/\/).*)?
^(mache)[n]? ((boot)|(drachenschiff)|(galeone)|(karavelle)|(langboot)|(tireme)|(plm[a-z]*))([ ]+(\/\/).*)?
^(mache)[n]? (schiff) [a-z0-9]{1,4}([ ]+(\/\/).*)?
^(mache)[n]? ((gebäude)|(gebaeude)|(burg)|(bergwerk)|(bergwerke)|(burgen)|(burg)|(höhle)|(höhlen)|(höhle)|(höhlen)|(höhle)|(höhlen)|(höhle)|(höhlen)|(höhle)|(höhlen)|(höhle)|(höhlen)|(hafen)|(häfen)|(holzfällerhütte)|(holzfällerhütten)|(holzfaellerhuetten)|(holzfaellerhuette)|(kathedrale)|(kathedralen)|(kirche)|(kirchen)|(kuechen)|(kueche)|(küchen)|(küche)|(leuchtturm)|(leuchttürme)|(minen)|(mine)|(monument)|(monumente)|(ruinen)|(ruine)|(saegewerk)|(sägewerke)|(sägewerk)|(saegewerke)|(höhle)|(höhlen)|(sattlerei)|(sattlereien)|(schiffswerft)|(schiffswerften)|(schmieden)|(schmiede)|(seehafen)|(seehäfen)|(steg)|(stege)|(steinbrüche)|(steinbruch)|(steingruben)|(steingrube)|(steuerturm)|(steuertürme)|(tempel)|(wegweiser)|(werkstätten)|(werkstatt))([ ]+(\/\/).*)?
^(mache)[n]? ((gebäude)|(gebaeude)|(burg)|(bergwerk)|(bergwerke)|(burgen)|(burg)|(höhle)|(höhlen)|(höhle)|(höhlen)|(höhle)|(höhlen)|(höhle)|(höhlen)|(höhle)|(höhlen)|(höhle)|(höhlen)|(hafen)|(häfen)|(holzfällerhütte)|(holzfällerhütten)|(holzfaellerhuetten)|(holzfaellerhuette)|(kathedrale)|(kathedralen)|(kirche)|(kirchen)|(kuechen)|(kueche)|(küchen)|(küche)|(leuchtturm)|(leuchttürme)|(minen)|(mine)|(monument)|(monumente)|(ruinen)|(ruine)|(saegewerk)|(sägewerke)|(sägewerk)|(saegewerke)|(höhle)|(höhlen)|(sattlerei)|(sattlereien)|(schiffswerft)|(schiffswerften)|(schmieden)|(schmiede)|(seehafen)|(seehäfen)|(steg)|(stege)|(steinbrüche)|(steinbruch)|(steingruben)|(steingrube)|(steuerturm)|(steuertürme)|(tempel)|(wegweiser)|(werkstätten)|(werkstatt)) [a-z0-9]{1,4}([ ]+(\/\/).*)?

^(unterhalte)[n]?([ ]+(\/\/).*)?
^(unterhalte)[n]?( [0-9]+)([ ]+(\/\/).*)?

^(handel)[n]? ((kaufe)[n]?) (")?(|(balsam)|(gewuerz)|(gewuerze)|(juwel)|(juwelen)|(säcke myrrhe)|(sack myrrhe)|(myrrhe)|(myhrre)|(myrre)|(oele)|(oel)|(pelz)|(pelze)|(ballen feinster seide)|(seide)|(weihrauch))(")?([ ]+(\/\/).*)?
^(handel)[n]? ((kaufe)[n]?)( [0-9]+) (")?(|(balsam)|(gewuerz)|(gewuerze)|(juwel)|(juwelen)|(säcke myrrhe)|(sack myrrhe)|(myrrhe)|(myhrre)|(myrre)|(oele)|(oel)|(pelz)|(pelze)|(ballen feinster seide)|(seide)|(weihrauch))(")?([ ]+(\/\/).*)?
^(handel)[n]? ((verkaufe)[n]?) (")?(|(balsam)|(gewuerz)|(gewuerze)|(juwel)|(juwelen)|(säcke myrrhe)|(sack myrrhe)|(myrrhe)|(myhrre)|(myrre)|(oele)|(oel)|(pelz)|(pelze)|(ballen feinster seide)|(seide)|(weihrauch))(")?([ ]+(\/\/).*)?
^(handel)[n]? ((verkaufe)[n]?)( [0-9]+) (")?(|(balsam)|(gewuerz)|(gewuerze)|(juwel)|(juwelen)|(säcke myrrhe)|(sack myrrhe)|(myrrhe)|(myhrre)|(myrre)|(oele)|(oel)|(pelz)|(pelze)|(ballen feinster seide)|(seide)|(weihrauch))(")?([ ]+(\/\/).*)?

^(treibe)[n]?([ ]+(\/\/).*)?
^(treibe)[n]?( [0-9]+)([ ]+(\/\/).*)?

^(beklaue)[n]? [a-z0-9]+([ ]+(\/\/).*)?

^nach( +(nw|no|o|so|sw|w|nordwesten|nordosten|osten|suedosten|suedwesten|westen|pause))+([ ]+(\/\/).*)?
^route( +(nw|no|o|so|sw|w|nordwesten|nordosten|osten|suedosten|suedwesten|westen|pause))+([ ]+(\/\/).*)?
^nach(( \([-+]?[0-9]+ [-+]?[0-9]+\))|( pause))+([ ]+(\/\/).*)?
^route(( \([-+]?[0-9]+ [-+]?[0-9]+\))|( pause))+([ ]+(\/\/).*)?

^folge(n)? (einheit) [a-z0-9]{1,4}([ ]+(\/\/).*)?
^folge(n)? (einheit) (temp) [a-z0-9]{1,4}([ ]+(\/\/).*)?
^folge(n)? (schiff) [a-z0-9]{1,4}([ ]+(\/\/).*)?
^@?(bewache)[n]?([ ]+(\/\/).*)?

^@?(botschaft temp)[a-z0-9]{1,4}( ).*
^@?(botschaft )[a-z0-9]{1,4}( ).*
^@?(botschaft einheit temp)[a-z0-9]{1,4}( ).*
^@?(botschaft einheit )[a-z0-9]{1,4}( ).*
^@?(botschaft )((partei)|(volk))( )[a-z0-9]{1,4}( ).*
^@?(botschaft )(an )?((region)|(alle))( ).*

^@?(bestätigt|bestaetigt) [1-9]{1}[0-9]{0,5}([ ]+(\/\/).*)?
^@?(bestätigt|bestaetigt) bis [1-9]{1}[0-9]{0,5}([ ]+(\/\/).*)?

^(faulenze)[n]?([ ]+(\/\/).*)?
^@?(default) .+
