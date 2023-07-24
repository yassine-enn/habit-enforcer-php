CREATE TABLE users (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE groups (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    text VARCHAR(50) NOT NULL,
    admin_id INT NOT NULL,
    FOREIGN KEY (admin_id) REFERENCES users(id)
);

CREATE TABLE group_members (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    group_id    INT  NOT NULL,
    member_id   INT NOT NULL,
    status boolean default false,

    FOREIGN KEY (group_id) REFERENCES groups(id),
    FOREIGN KEY (member_id) REFERENCES users(id)

);

CREATE TABLE tasks (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    text VARCHAR(50) NOT NULL,
    repeats boolean NOT NULL,
    color varchar(20) default "blue",
    difficulty int default 0,
    periodicity varchar(20) default "daily",
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    group_id INT  NOT NULL,
    user_id INT  NOT NULL,
    FOREIGN KEY (group_id) REFERENCES groups(id),
    FOREIGN KEY (user_id) REFERENCES users(id)

);

CREATE TABLE checks (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    group_id INT  NOT NULL,
    task_id INT  NOT NULL,
    FOREIGN KEY (group_id) REFERENCES groups(id),
    FOREIGN KEY (task_id) REFERENCES tasks(id),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP 
);

CREATE TABLE log (
    id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    task_id INT  NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    score INT NOT NULL,
    FOREIGN KEY (task_id) REFERENCES tasks(id)
);

