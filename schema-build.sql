CREATE TABLE member (
    id                     INTEGER PRIMARY KEY AUTO_INCREMENT,
    email                  VARCHAR(254) NOT NULL UNIQUE,
    name                   VARCHAR(60),
    username               VARCHAR(20) NOT NULL UNIQUE,
    birthday               DATE,
    password               CHAR(60),
    registration_date      DATE,
    profile_photo_filename VARCHAR(100) DEFAULT 'photo.png',
    cover_photo_filename   VARCHAR(100) DEFAULT 'cover.png',
    position               INTEGER DEFAULT 0,
    description            TEXT,
    token                  VARCHAR(64),
    confirmation           VARCHAR(64)
);

CREATE TABLE subscription (
    member_following INTEGER REFERENCES member (id),
    member_followed  INTEGER REFERENCES member (id),
    PRIMARY KEY (member_following, member_followed)
);

CREATE TABLE track (
    id               INTEGER PRIMARY KEY AUTO_INCREMENT,
    title            VARCHAR(60),
    description      TEXT,
    genre            VARCHAR(30),
    track_filename   VARCHAR(100),
    photo_filename   VARCHAR(100),
    publication_date DATETIME,
    member           INTEGER NOT NULL REFERENCES member (id)
);

CREATE TABLE listening (
    member  INTEGER NOT NULL DEFAULT 0 REFERENCES member (id),
    track   INTEGER NOT NULL DEFAULT 0 REFERENCES track (id),
    listening_date DATETIME
);

CREATE TABLE likes (
    member  INTEGER NOT NULL REFERENCES member (id),
    track   INTEGER NOT NULL REFERENCES track (id),
    PRIMARY KEY (member, track)
);

CREATE TABLE playlist (
    id     INTEGER PRIMARY KEY AUTO_INCREMENT,
    name   VARCHAR(60),
    member INTEGER NOT NULL REFERENCES member (id)
);

CREATE TABLE inclusion (
    playlist INTEGER REFERENCES playlist (id),
    track    INTEGER REFERENCES track (id),
    PRIMARY KEY (playlist, track)
);

CREATE TABLE post (
    id               INTEGER PRIMARY KEY AUTO_INCREMENT,
    content          TEXT,
    publication_date DATETIME,
    member           INTEGER NOT NULL REFERENCES member (id)
);

CREATE TABLE comment (
    id               INTEGER PRIMARY KEY AUTO_INCREMENT,
    content          TEXT,
    publication_date DATETIME,
    post             INTEGER REFERENCES post (id),
    track            INTEGER  REFERENCES track (id),
    event            INTEGER REFERENCES event (id),
    member           INTEGER NOT NULL REFERENCES member (id)
);

CREATE TABLE events (
    id                  INTEGER PRIMARY KEY AUTO_INCREMENT,
    name                VARCHAR(60),
    description         TEXT,
    capacity            INTEGER,
    event_date          DATE,
    background_filename VARCHAR(100) DEFAULT 'background.png',
    address             VARCHAR(100) DEFAULT 'Not defined',
    publication_date    DATETIME,
    member              INTEGER NOT NULL REFERENCES member (id)
);

CREATE TABLE registration (
    member             INTEGER REFERENCES MEMBRE (id),
    events             INTEGER REFERENCES events (id),
    registration_token VARCHAR(64),
    PRIMARY KEY (member, events)
);
