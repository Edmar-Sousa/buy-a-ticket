
CREATE TABLE batchs (
    id             CHAR(36) PRIMARY KEY,
    total          INT      NOT NULL DEFAULT (100),
    sold           INT      NOT NULL DEFAULT (0),
    reserved       INT      NOT NULL DEFAULT (0),
    available      INT      NOT NULL DEFAULT (100)
);

CREATE TABLE reservations (
    id             CHAR(36) PRIMARY KEY,

    quantity       INT      NOT NULL,
    cpf            CHAR(11) NOT NULL,

    status         CHAR(100)    NOT NULL,
    expires_at     TIMESTAMPTZ  NOT NULL,

    CONSTRAINT unique_cpf UNIQUE(cpf)
);


DO $$
BEGIN
  INSERT INTO batchs (id, total, sold, reserved, available)
  VALUES ('e41b2f0c-03fa-4edb-8307-f7519862f8eb', 100, 0, 0, 100);
END; $$