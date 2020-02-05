all:

migrate:
	phinx migrate

seed:
	phinx seed:run

sql:
	sqlite3 -header var/database.sqlite
