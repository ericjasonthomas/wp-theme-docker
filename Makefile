db_import:
	docker-compose exec -T db mysql -u wordpress -p wordpress wordpress < db/thebsid1_wrd1_back.sql
database:
	docker-compose exec db mysql -u wordpress -p