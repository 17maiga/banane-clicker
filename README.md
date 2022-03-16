# banane-clicker

A sister project to [pasteque-clicker](https://www.github.com/17maiga/pasteque-clicker).
Essentially the same thing except it's a different fruit and it is built using the symfony framework. It's also a school project.

*La banane est le fruit ou la baie dérivant de l’inflorescence du bananier. Les bananes sont des fruits très généralement stériles issus de variétés domestiquées. Seuls les fruits des bananiers sauvages et de quelques cultivars domestiques contiennent des graines. Les bananes sont généralement jaunes avec des taches brunâtres lorsqu'elles sont mûres et vertes quand elles ne le sont pas.*

*Les bananes constituent un élément essentiel du régime alimentaire dans certaines régions, comme en Ouganda, qui offrirait une cinquantaine de variétés de ce fruit.*


### SETUP INSTRUCTIONS

You must have a [MySQL](https://www.mysql.com/fr/downloads/) server installed and running, as well as [Composer](https://getcomposer.org/download/), [Symfony-cli](https://symfony.com/download), and [NodeJS](https://nodejs.dev/) installed.

Import the database from the `banane.sql` file to your MySQL server.

Set up your database credentials in the `.env` file (replace the `db_user`, `db_password` and `db_name` fields on line 30 with your MySQL user credentials and the database name). The database user must have permissions on the database in order for this to work.

Open a terminal in the project's root folder and run these commands:

```
composer install
npm install
npm run build
symfony server:start
```
You can now open your browser and head to `localhost:8000/`. You are now on the banane-clicker website !

### PROJECT DESCRIPTION

This website is organised around 6 main parts:
- The home page, which simply welcomes the user to the website
- The register and login pages, which allow you to do exactly what you would think they do
- The game page, only accessible to logged-in users, which allows the user to play banane-clicker
  - You can increase the number of bananas you own by clicking on the big banana on the left.
  - The buttons on the right allow you to purchase upgrades that will automatically increase your banana count every second by a certain amount
  - The game currently has two upgrades:
    - A banana tree, which costs 5 bananas and gives you 1 banana per second
    - A banana farm, which costs 100 bananas and gives you 10 bananas per second
  - You can save your progress with the `SAVE` button
- The account page, only accessible to logged-in users, which allows you to edit your username and reset your game progress if you want to
- The moderation page, only accessible to moderators, which allows moderators to delete users and admins to promote/demote moderators
- The admin page, only accessible to administrators, which allows admins to add/edit/delete in-game upgrades

The game has preset accounts, feel free to use them in order to try the website's functionality:
```
login: admin        password: pw_admin          role: admin
login: moderator    password: pw_moderator      role: moderator
login: user         password: pw_user           role: user
```

### POSSIBLE UPGRADES

I have learned from my mistakes doing [pasteque-clicker](https://www.github.com/17maiga/pasteque-clicker), and I have better organised my code around the project and what I wanted to do with it from the start.
The website will work around the upgrades in the database and all entities are interconnected together, allowing for easy creation/removal of upgrades to the game without breaking everything.

I would have liked to make the website, and especially the game window, a little prettier, perhaps with a better layout or more graphical assets. 
I would also have liked to add a proper moderation system, with reports and suspensions, and perhaps add a forum to allow users to discuss the game and/or other topics.
It would also be fun to allow admins to change the price of upgrades based on how many upgrades are owned by the user, and specify a mathematical function to calculate this price increase for each upgrade if they wish to do so.

Apart from that, I don't think I have any upgrades in mind for this project.
Better comments in the code, in order to make it more readable, perhaps :)

### CONCLUSION

I hope you have fun with banane-clicker! I hope to continue working on this after submission, however I won't push anything until I get my grade.

Enjoy banane-clicker!