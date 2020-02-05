REMOTE=vhost.umonkey.net
FOLDER=hosts/bjt.umonkey.net

all:

deploy:
	rsync -avz -e ssh config public src templates vendor phinx.yml $(REMOTE):$(FOLDER)/

migrate:
	phinx migrate

seed:
	phinx seed:run

sql:
	sqlite3 -header var/database.sqlite

sql-remote:
	ssh -t $(REMOTE) sqlite3 -header $(FOLDER)/var/database.sqlite
