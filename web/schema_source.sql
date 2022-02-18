CREATE DATABASE taskforce
    DEFAULT CHARACTER SET utf8mb4
    DEFAULT COLLATE utf8mb4_general_ci;

USE taskforce;

CREATE TABLE category (
    PRIMARY KEY (id),
    id    SMALLINT UNSIGNED  NOT NULL  AUTO_INCREMENT,
    name  VARCHAR(50)        NOT NULL  UNIQUE        ,
    icon  VARCHAR(50)        NOT NULL
);

CREATE TABLE city (
    PRIMARY KEY (id),
    id           INT UNSIGNED    NOT NULL  AUTO_INCREMENT,
    name         VARCHAR(50)     NOT NULL                ,
    latitude     DECIMAL(10, 8)  NOT NULL                ,
    longitude    DECIMAL(11, 8)  NOT NULL
);

CREATE TABLE task_status (
    PRIMARY KEY (id),
    id    TINYINT UNSIGNED  NOT NULL  AUTO_INCREMENT,
    name  VARCHAR(50)       NOT NULL  UNIQUE
);

CREATE TABLE role (
    PRIMARY KEY (id),
    id    TINYINT UNSIGNED  NOT NULL  AUTO_INCREMENT,
    name  VARCHAR(50)       NOT NULL  UNIQUE
);

CREATE TABLE user (
    PRIMARY KEY (id),
    id                INT UNSIGNED      NOT NULL AUTO_INCREMENT           ,
    name              VARCHAR(100)      NOT NULL                          ,
    email             VARCHAR(50)       NOT NULL UNIQUE                   ,
    password          VARCHAR(255)      NOT NULL                          ,
    city_id           INT UNSIGNED      NOT NULL                          ,
    birthdate         DATE                                                ,
    photo             VARCHAR(255)                                        ,
    phone             VARCHAR(11)                                         ,
    telegram          VARCHAR(64)                                         ,
    self_description  TEXT                                                ,
    role_id           TINYINT UNSIGNED  NOT NULL                          ,
    fails_count       INT UNSIGNED      NOT NULL DEFAULT 0                ,
    date_registered   DATETIME          NOT NULL DEFAULT CURRENT_TIMESTAMP,

    INDEX (city_id),
    INDEX (role_id),

    CONSTRAINT fk_user_city  FOREIGN KEY (city_id)  REFERENCES city(id),
    CONSTRAINT fk_user_role  FOREIGN KEY (role_id)  REFERENCES role(id)
);

CREATE TABLE chosen_category (
    PRIMARY KEY (user_id, category_id),
    user_id      INT UNSIGNED       NOT NULL,
    category_id  SMALLINT UNSIGNED  NOT NULL,

    INDEX (category_id),

    CONSTRAINT fk_chosen_category_user      FOREIGN KEY (user_id)      REFERENCES user(id),
    CONSTRAINT fk_chosen_category_category  FOREIGN KEY (category_id)  REFERENCES category(id)
);

CREATE TABLE task (
    PRIMARY KEY (id),
    id              INT UNSIGNED       NOT NULL AUTO_INCREMENT           ,
    overview        VARCHAR(255)       NOT NULL                          ,
    description     TEXT               NOT NULL                          ,
    category_id     SMALLINT UNSIGNED  NOT NULL                          ,
    city_id         INT UNSIGNED                                         ,
    budget          INT UNSIGNED                                         ,
    deadline        DATE                                                 ,
    task_status_id  TINYINT UNSIGNED   NOT NULL                          ,
    customer_id     INT UNSIGNED       NOT NULL                          ,
    contractor_id   INT UNSIGNED                                         ,
    score           TINYINT                                              ,
    feedback        TEXT                                                 ,
    date_created    DATETIME           NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT score_limits CHECK (score >= 1 AND score <= 5),

    INDEX (category_id),
    INDEX (city_id),
    INDEX (task_status_id),
    INDEX (customer_id),
    INDEX (contractor_id),

    CONSTRAINT fk_task_category    FOREIGN KEY (category_id)         REFERENCES category(id)   ,
    CONSTRAINT fk_task_city        FOREIGN KEY (city_id)             REFERENCES city(id)       ,
    CONSTRAINT fk_task_status      FOREIGN KEY (task_status_id)      REFERENCES task_status(id),
    CONSTRAINT fk_task_customer    FOREIGN KEY (customer_id)         REFERENCES user(id)       ,
    CONSTRAINT fk_task_contractor  FOREIGN KEY (contractor_id)       REFERENCES user(id)
);

CREATE TABLE response (
    PRIMARY KEY (id),
    id            INT UNSIGNED  NOT NULL AUTO_INCREMENT           ,
    task_id       INT UNSIGNED  NOT NULL                          ,
    user_id       INT UNSIGNED  NOT NULL                          ,
    price         INT UNSIGNED                                    ,
    comment       TEXT                                            ,
    date_created  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,

    INDEX (task_id),
    INDEX (user_id),

    CONSTRAINT fk_response_task  FOREIGN KEY (task_id)  REFERENCES task(id),
    CONSTRAINT fk_response_user  FOREIGN KEY (user_id)  REFERENCES user(id)
);

CREATE TABLE task_file (
    PRIMARY KEY (id),
    id            BIGINT UNSIGNED  NOT NULL AUTO_INCREMENT           ,
    task_id       INT UNSIGNED     NOT NULL                          ,
    path          VARCHAR(255)                                       ,
    date_created  DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,

    INDEX (task_id),

    CONSTRAINT fk_task_file_task  FOREIGN KEY (task_id)  REFERENCES task(id)
);
