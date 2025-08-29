up: ; docker compose up -d
down: ; docker compose down
api-shell: ; docker compose exec api bash
db-cli: ; docker compose exec db mysql -udrivn -pdrivnpass drivncook
