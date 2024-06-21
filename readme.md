<h1 align="center">
  <a href="https://api.germondai.com" target="_blank">
    <img align="center" src="https://skillicons.dev/icons?i=php,mysql" /><br/><br/>
    <span>PHP RESTful API</span>&nbsp;
  </a>
</h1>

**PHP RESTful API** with **Doctrine** integration. Own **Api** & **Entity Controller** that handles routing of **Model/Action** and **Entity** with **CRUD options** based on **Request Method**. Doctrine **Console**, **Entities** and **Migrations** to create flawless **ORM Schemas**. Custom useful **Utils** like **Token** that manages **JSON Web Tokens** (JWT). Pre-done **Auth Model**, **User Entity** connected with **Media Entity** by **OneToMany Relation** to keep track of **Author** of **uploaded** media **files** (**optimize**, **resize** and **image format/type change** included). Easy **configuration** and safe **.env** variables.

## ⚡️ Features

**Overview**

- Own REST API System
  - Routing
  - Auth
  - CRUD
  - Media
    - Upload
    - Optimize
    - Resize
    - Quality
    - Format
- Doctrine
  - ORM
  - DBAL
  - Entities
  - Migrations
  - Annotations
- Custom Utils
  - Helper
  - Database
  - Doctrine
  - JSON Web Tokens (JWT)
- Nette
  - Database Explorer
  - Tracy
- Environment (.env)

## 🧬 Structure

**api/** - App's Main Code (Controller, Entity, Model)\
**bin/** - Console for Doctrine\
**migrations/** - Doctrine DB Migrations\
**public/** - Accessible from Outside (Routing, Media)\
**src/** - Developer Source Files (Assets, Includes, Utils)\
**temp/** - Storage for Temporary Files and Logs

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

- Database Credentials
- JWT Secret and Algorithm

```bash
# to dupe example.env as .env
cp example.env .env
```

## 📝 Entity Guide

Entity CRUD operations depends on Request Methods\
Entity Schema returns tables / table with columns

### Routes

Operations: `/[entity]/[id]`

- GET - Read
- POST - Create
- PUT - Replace
- PATCH - Update
- DELETE - Delete
- OPTIONS - Preflight (always return 200)

Schema: `/schema/[entity]`

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

---

<p align="center">
    <span>Made with ❤️ by</span>
    <a href="https://github.com/germondai" target="_blank">@germondai</a>
</p>
