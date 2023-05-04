NAME = camagru

all: $(NAME)

$(NAME):
	docker-compose -f srcs/docker-compose.yml build
	docker-compose -f srcs/docker-compose.yml up -d

clean:
	docker-compose -f srcs/docker-compose.yml stop
	docker-compose -f srcs/docker-compose.yml down

re: clean all

.PHONY: all clean 