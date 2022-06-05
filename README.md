# Connect Four

This project is about learning how to properly document, mantain, fix, develop and test software, using git for version control and GitHub to not only host that code, but to also try out other features it has, such as automatically testing code in commits. I'll also be learning how to use Laravel, a widely used PHP web framework, which the mentioned project runs on.

To achieve that, I'll be working from a simple implementation of the well known Connect Four game, given the hypothetical scenario where the previous dev quit without notice. This makes it so that I have to understand the already written code, find out and document how to set up the website where the game lies in, create automated tests to help keep functionality as originally intended, restructure the code to make it more readable and maintainable, and add new features to meet the requirements of the (also hypothetical) employer.

Carried out as part of the subject "Adaptación al Ambiente de Trabajo" (AAE), Instituto Politécnico Superior (6to INFO, 2022).

## Dependencies

This project uses [ddev](https://github.com/drud/ddev) to get it running. It contains all the components required for the installation, so there are no other dependencies.

For more info and instructions on how to install it, check out its [documentation](https://ddev.readthedocs.io/en/stable/).

## Installation

Clone the repository as you normally would, and start the project with ddev inside the new directory.

Apart from that, you'll only have to set up your environment by renaming the provided `.env.example` file to `.env` and generating an encryption key. The file is provided this way in case you may want to modify it according to your needs (see the [Laravel documentation](https://laravel.com/docs/9.x/configuration#environment-configuration) for more info). But if you just want to run it quickly, simply copy the file as shown and it should work just fine.

```shell
# Start the project
ddev start
# Copy the example .env file, you can then modify it as you wish
cp .env.example .env
# Generate a key for encryption
ddev php artisan key:generate
```

From now on, you would be able to start or stop the project with `ddev start` and `ddev stop` respectively.

## Usage

Once installed and running, by default, you would be able to play the game in your local machine at https://cuatroenlinea.ddev.site/jugar/1.

Note that the last number in the url represents the alternated sequence of movements made by the two players, and you have to have *at least* one number in the sequence for the game to work. So for example, if the url ends with `/jugar/6`, the game will start with a red token placed at the bottom of the 6th column (counting from the left).

Otherwise, if the link were to end with `/jugar/1234567`, the tokens would be placed in the following way: the first player placed a token in the first column, the second player placed a token in the second column, the first player placed a token in the third column, and so on.

<p align="center">
    <img style="width: 30%;" src="https://user-images.githubusercontent.com/68936651/172038260-1ebc224e-162d-4564-a9aa-6cbd07bd8388.png" />
    <img style="width: 30%;" src="https://user-images.githubusercontent.com/68936651/172038300-7c9e1400-4ae8-4281-9bab-6b83111ca422.png" />
</p>

To make a new movement, move your mouse above the column where the corresponding player wants to place a new token in. This will show you a spinning wheel-like shape, colored red or light blue, depending on whether it's the first or second player's turn. As expected, clicking it will place a token in that column, thus leading you to the updated website with a new number in the sequence.

<p align="center"><img width="30%" src="https://user-images.githubusercontent.com/68936651/172038672-89aa871b-a590-493a-83a5-4a0aee45ea9e.png" /></p>

**Currently, there's no feedback when a player wins (i.e. manages to connect four tokens by placing them adjacently in a row, column or diagonal)** (to be added soon).
