NAME = camagru

all: $(NAME)

$(NAME):
	docker-compose -f srcs/docker-compose.yml --project-name $(NAME) up --build -d

clean:
	docker-compose -f srcs/docker-compose.yml --project-name $(NAME) down
	docker system prune -f

re: clean all

.PHONY: all clean 