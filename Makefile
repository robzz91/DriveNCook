up: ; docker compose up -d --build
down: ; docker compose down
api-shell: ; docker compose exec api bash
db-cli: ; docker compose exec db mysql -udrivnuser -pdrivnpass drivncook_m2
