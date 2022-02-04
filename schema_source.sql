CREATE DATABASE taskforce
    DEFAULT CHARACTER SET utf8mb4
    DEFAULT COLLATE utf8mb4_general_ci;

USE taskforce;

CREATE TABLE categories (
    PRIMARY KEY (category_id),
    category_id   SMALLINT UNSIGNED  NOT NULL  AUTO_INCREMENT,
    category_name VARCHAR(50)        NOT NULL  UNIQUE
);

CREATE TABLE cities (
    PRIMARY KEY (city_id),
    city_id    INT UNSIGNED  NOT NULL  AUTO_INCREMENT,
    city_name  VARCHAR(50)   NOT NULL
);

CREATE TABLE task_statuses (
    PRIMARY KEY (task_status_id),
    task_status_id    TINYINT UNSIGNED  NOT NULL  AUTO_INCREMENT,
    task_status_name  VARCHAR(50)       NOT NULL  UNIQUE
);

CREATE TABLE roles (
    PRIMARY KEY (role_id),
    role_id    TINYINT UNSIGNED  NOT NULL  AUTO_INCREMENT,
    role_name  VARCHAR(50)       NOT NULL  UNIQUE
);

CREATE TABLE users (
    PRIMARY KEY (user_id),
    user_id                INT UNSIGNED      NOT NULL AUTO_INCREMENT           ,
    user_name              VARCHAR(100)      NOT NULL                          ,
    user_email             VARCHAR(50)       NOT NULL UNIQUE                   ,
    user_password          VARCHAR(255)      NOT NULL                          ,
    city_id                INT UNSIGNED      NOT NULL                          ,
    user_birthdate         DATE                                                ,
    user_photo             VARCHAR(255)                                        ,
    user_phone             VARCHAR(11)                                         ,
    user_telegram          VARCHAR(64)                                         ,
    user_self_description  TEXT                                                ,
    role_id                TINYINT UNSIGNED  NOT NULL                          ,
    user_fails_count       INT UNSIGNED      NOT NULL DEFAULT 0                ,
    user_date_registered   DATETIME          NOT NULL DEFAULT CURRENT_TIMESTAMP,

    INDEX (city_id),
    INDEX (role_id),

    CONSTRAINT fk_user_city  FOREIGN KEY (city_id)  REFERENCES cities(city_id),
    CONSTRAINT fk_user_role  FOREIGN KEY (role_id)  REFERENCES roles(role_id)
);

CREATE TABLE chosen_categories (
    PRIMARY KEY (user_id, category_id),
    user_id     INT UNSIGNED       NOT NULL,
    category_id SMALLINT UNSIGNED  NOT NULL,

    INDEX (category_id),

    CONSTRAINT fk_category_user      FOREIGN KEY (user_id)      REFERENCES users(user_id),
    CONSTRAINT fk_category_category  FOREIGN KEY (category_id)  REFERENCES categories(category_id)
);

CREATE TABLE tasks (
    PRIMARY KEY (task_id),
    task_id             INT UNSIGNED       NOT NULL AUTO_INCREMENT           ,
    task_overview       VARCHAR(255)       NOT NULL                          ,
    task_description    TEXT               NOT NULL                          ,
    category_id         SMALLINT UNSIGNED  NOT NULL                          ,
    city_id             INT UNSIGNED                                         ,
    task_coortinates    POINT                                                ,
    task_budget         INT UNSIGNED                                         ,
    task_deadline       DATE                                                 ,
    task_status_id      TINYINT UNSIGNED   NOT NULL                          ,
    task_customer_id    INT UNSIGNED       NOT NULL                          ,
    task_contractor_id  INT UNSIGNED                                         ,
    task_score          TINYINT                                              ,
    task_feedback       TEXT                                                 ,
    task_date_created   DATETIME           NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT task_score_limits CHECK (task_score >= 1 AND task_score <= 5),

    INDEX (category_id),
    INDEX (city_id),
    INDEX (task_status_id),
    INDEX (task_customer_id),
    INDEX (task_contractor_id),

    CONSTRAINT fk_task_category    FOREIGN KEY (category_id)         REFERENCES categories(category_id)      ,
    CONSTRAINT fk_task_city        FOREIGN KEY (city_id)             REFERENCES cities(city_id)              ,
    CONSTRAINT fk_task_status      FOREIGN KEY (task_status_id)      REFERENCES task_statuses(task_status_id),
    CONSTRAINT fk_task_customer    FOREIGN KEY (task_customer_id)    REFERENCES users(user_id)               ,
    CONSTRAINT fk_task_contractor  FOREIGN KEY (task_contractor_id)  REFERENCES users(user_id)
);

CREATE TABLE responses (
    PRIMARY KEY (response_id),
    response_id            INT UNSIGNED  NOT NULL AUTO_INCREMENT           ,
    task_id                INT UNSIGNED  NOT NULL                          ,
    user_id                INT UNSIGNED  NOT NULL                          ,
    response_price         INT UNSIGNED                                    ,
    response_comment       TEXT                                            ,
    response_date_created  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,

    INDEX (task_id),
    INDEX (user_id),

    CONSTRAINT fk_response_task  FOREIGN KEY (task_id)  REFERENCES tasks(task_id),
    CONSTRAINT fk_response_user  FOREIGN KEY (user_id)  REFERENCES users(user_id)
);

CREATE TABLE tasks_files (
    PRIMARY KEY (task_file_id),
    task_file_id            BIGINT UNSIGNED  NOT NULL AUTO_INCREMENT           ,
    task_id                 INT UNSIGNED     NOT NULL                          ,
    task_file_path          VARCHAR(255)                                       ,
    task_file_date_created  DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,

    INDEX (task_id),

    CONSTRAINT fk_task_file_task  FOREIGN KEY (task_id)  REFERENCES tasks(task_id)
);
