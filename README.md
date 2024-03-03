# Kaufland - Coding Task - Lea Corsi

This project contains a command-line program in PHP that processes a local XML file and inserts its data into a SQLite database. 

## Table of Contents

- [Introduction](#introduction)
- [Features](#features)
- [Installation](#installation)
- [Usage](#usage)
- [Reflections](#reflections)

## Introduction

This project contains a command-line program in PHP that processes a local XML file and inserts its data into a SQLite database. 
The program, developed in PHP, is designed to offer flexibility; providing not only automatic saving based completely on the XML structure but also data storage configuration. 
In addition, the way the code is structured guarantees ease of extension for future improvements. 

## Features

- **XML data processing:** The program parses the contents of the local XML file (feed.xml), ensuring data extraction.
- **Database integration:** Using SQLite as the default database, the program saves the parsed XML data. The architecture includes an abstract class, which opens the way for potential extensions to incorporate additional database options in the future.
- **Configurability:** Command line parameters can be used to define the structure of the table(s) in the database.
- **Error logging:** Errors encountered during program execution are logged in a dedicated log file, facilitating troubleshooting and debugging processes.
- **Automated testing:** Although a formal testing framework has not been implemented, the program includes some automated tests to verify its functionality.


## Installation
### Prerequisites
Before installing and running the program, ensure that PHP is installed on your system and SQLite3 is enabled in the php.ini configuration file.
### Install
To install you need to clone the repository :
```
git clone https://github.com/CorsiLea/Kaufland.git
```

## Usage

To execute the program:
1. Open the terminal
2. Navigate to the project directory
3. Run the program
    1. With automatic configuration
        ```
        php ./src/Main.php ./Data/feed.xml
        ```
        Replace **feed.xml** with the xml you whant to import
    2. With manual configuration (allow to chose wich column to save)
        ```
        php ./src/Main.php ./Data/feed.xml <tableName> <column1,column2,...>  <tableName2> <column1,column2,...> ...
        ```

## XML Format
the xml has to be formatted as follow :
```
<dbName>
    <tableName>
        <columnName>value</columnName>
        [...]
    </tableName>
    [...]
</dbName>
```


## Reflections
Some reflections about the project
### Database
Some points in the way I handled the database do not fully satisfy me :

- **Primary Key:** SQLite requires each table to have at least one column, which led me to add an autoincremental id. One improvement would be to allow users to specify the primary key column, potentially via XML attributes or command line options.

- **Column typing:** All columns in the SQLite table were defined as text, without specificity. To improve data integrity and query efficiency, it would be interesting to find a way to infer the type or be able to configure it in the XML or command line.

### Test
Having studied automated testing in school, I recognize its importance and benefits in ensuring software reliability and growth. 
Despite its importance, I have not had the opportunity to apply automated testing in professional projects. This absence is seen in the test coverage of the current project.
Although I have added some testing to the project, it is evident that there are numerous features and methods that remain untested.
