NAME = camagru

all: $(NAME)

$(NAME):
	docker-compose --env-file srcs/.env -f srcs/docker-compose.yml build
	docker-compose --env-file srcs/.env -f srcs/docker-compose.yml up -d

clean:
	docker-compose --env-file srcs/.env -f srcs/docker-compose.yml stop
	docker-compose --env-file srcs/.env -f srcs/docker-compose.yml down

.PHONY: all clean 