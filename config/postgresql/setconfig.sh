# echo "shared_preload_libraries = 'pg_cron'" >> /var/lib/postgresql/data/postgresql.conf
# echo "cron.database_name = 'postgres'" >> /var/lib/postgresql/data/postgresql.conf
yes | cp /docker-entrypoint-initdb.d/postgresql.conf /var/lib/postgresql/data/postgresql.conf