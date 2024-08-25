DROP TABLE IF EXISTS abstimmung;
DROP TABLE IF EXISTS vorlage;

CREATE TABLE IF NOT EXISTS abstimmung
(
    id          INTEGER PRIMARY KEY,
    external_id VARCHAR UNIQUE NOT NULL,
    title       VARCHAR        NOT NULL,
    date        DATE           NOT NULL
);

CREATE TABLE IF NOT EXISTS vorlage
(
    id                 INTEGER PRIMARY KEY,
    abstimmung_id      INTEGER        NOT NULL,
    external_id        INTEGER UNIQUE NOT NULL,
    title              VARCHAR        NOT NULL,
    vorlage_angenommen BOOLEAN        NOT NULL CHECK ( vorlage_angenommen IN (0, 1)),
    FOREIGN KEY (abstimmung_id) REFERENCES abstimmung (id)
);

