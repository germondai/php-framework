<h1 align="center">
  <a href="https://germondai.rf.gd" target="_blank">
    <img align="center" src="https://skillicons.dev/icons?i=php,mysql" /><br/><br/>
    <span>PHP RESTful API</span>&nbsp;
  </a>
</h1>

**PHP RESTful API** with **Doctrine** integration and **Nette DB Explorer**. Own **model/action routing** and **ApiController**. Doctrine **Console**, **Entities** and **Migrations** to create flawless **ORM Schemas**. Custom useful **Utils** like **Token** that manages **JSON Web Tokens** (JWT). Pre-done **BaseEntity**, **AuthModel** and **UserEntity** connected with **ArticleEntity** (OneToMany). Easy **configuration** and safe **.env** variables.

## ⚡️ Features

**Overview**

- Own REST API System
  - Routing
  - Auth
- Doctrine
  - ORM
  - DBAL
  - Entities
  - Migrations
- Custom Utils
  - Helper
  - Database
  - Doctrine
  - JSON Web Tokens
- Nette
  - Database Explorer
  - Tracy
- Environment (.env)

## 🧬 Structure

**api/** - accessible on /api/_model_/_action_, (models and entities)\
**bin/** - Console for Doctrine\
**migrations/** - Doctrine DB Migrations\
**src/** - contains includes, utils and dev assets\
**temp/** - Nette DB Temp Storage

## 🧠 Technologies

- <a href="https://www.php.net/" target="_blank">PHP</a>
- <a href="https://www.doctrine-project.org/" target="_blank">Doctrine</a>
- <a href="https://doc.nette.org/en/database" target="_blank">Nette DB</a>
- <a href="https://jwt.io/" target="_blank">JSON Web Tokens (JWT)</a>

## 🛠️ Installation Instructions

Requirements

- 👨‍💻 <a href="https://getcomposer.org/" target="_blank">Composer</a>

**Install dependencies**

```bash
composer install
```

**Setup .env**

Fill in placeholders for database credentials in the .env file

```bash
# to dupe example.env as .env
cp example.env .env
```

## 📚 Doctrine Guide

The Doctrine console is in "_bin/console_"\
EntityManager config location "_src/Utils/Doctrine.php_"\
Base migrations config, which is in root "_migrations.php_"\
And migration files are stored in "_migrations/_"

### Console

```bash
# To run doctrine console
php bin/console ...

# if you need commands list
php bin/console list
```

<p align="center">
    <span>Made with ❤️ by</span>
    <a href="https://github.com/germondai" target="_blank">@germondai</a>
</p>
