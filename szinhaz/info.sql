
DROP table if exists szekhely;
CREATE TABLE szekhely (id INT NOT NULL AUTO_INCREMENT , nev varchar(30) NOT NULL , belfoldi tinyint(1) NOT NULL, PRIMARY KEY (id)) ENGINE = InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;

INSERT INTO szekhely(nev,belfoldi) SELECT DISTINCT szekhely,belfoldi FROM szinhaz ORDER BY szekhely;
ALTER TABLE szinhaz ADD szekhely_id INT NULL AFTER szekhely;
ALTER TABLE szinhaz ADD INDEX(szekhely_id);
UPDATE szinhaz SET szekhely_id=(SELECT szekhely.id FROM szekhely WHERE szekhely.nev=szinhaz.szekhely);
ALTER TABLE szinhaz DROP szekhely;

DROP table if exists mufaj;
CREATE TABLE mufaj (id INT NOT NULL AUTO_INCREMENT , nev varchar(30) NOT NULL , PRIMARY KEY (id)) ENGINE = InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;
INSERT INTO mufaj(nev) SELECT DISTINCT mufaj FROM eloadas ORDER BY mufaj;
ALTER TABLE eloadas ADD mufaj_id INT NOT NULL AFTER datum;
ALTER TABLE eloadas ADD INDEX(mufaj_id);
UPDATE eloadas SET mufaj_id=(SELECT mufaj.id FROM mufaj WHERE mufaj.nev=eloadas.mufaj);
ALTER TABLE eloadas DROP mufaj;

DROP table if exists nyelv;
CREATE TABLE nyelv (id INT NOT NULL AUTO_INCREMENT , nev varchar(15) NOT NULL , PRIMARY KEY (id)) ENGINE = InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;
INSERT INTO nyelv(nev) SELECT DISTINCT nyelv FROM eloadas ORDER BY nyelv;
ALTER TABLE eloadas ADD nyelv_id INT NOT NULL AFTER nyelv;
ALTER TABLE eloadas ADD INDEX(nyelv_id);
UPDATE eloadas SET nyelv_id=(SELECT nyelv.id FROM nyelv WHERE nyelv.nev=eloadas.nyelv);
ALTER TABLE eloadas DROP nyelv;

DROP table if exists tulajdonsagnev;
CREATE TABLE tulajdonsagnev (id INT NOT NULL AUTO_INCREMENT , nev varchar(15) NOT NULL , PRIMARY KEY (id)) ENGINE = InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;
INSERT INTO tulajdonsag(nev) SELECT DISTINCT nev FROM tulajdonsag ORDER BY nev;
ALTER TABLE tulajdonsag ADD tulajdonsagnev_id INT NOT NULL AFTER nev;
ALTER TABLE tulajdonsag ADD INDEX(tulajdonsagnev_id);
UPDATE tulajdonsag SET tulajdonsagnev_id=(SELECT tulajdonsagnev.id FROM tulajdonsagnev WHERE tulajdonsagnev.nev=tulajdonsag.tulajdonsag.nev);
ALTER TABLE tulajdonsag DROP nev;

ALTER TABLE szinhaz drop CONSTRAINT IF EXISTS szekhely_fk;
ALTER TABLE szinhaz
  ADD CONSTRAINT szekhely_fk FOREIGN KEY (szekhely_id) REFERENCES szekhely (id) on delete set null;

ALTER TABLE eloadas drop CONSTRAINT IF EXISTS mufaj_fk,
                    drop CONSTRAINT IF EXISTS nyelv_fk;

ALTER TABLE eloadas
  ADD CONSTRAINT mufaj_fk FOREIGN KEY (mufaj_id) REFERENCES mufaj (id) on delete set null,
  ADD CONSTRAINT nyelv_fk FOREIGN KEY (nyelv_id) REFERENCES nyelv (id) on delete set null;

ALTER TABLE tulajdonsag drop CONSTRAINT IF EXISTS tulajdonsagnev_fk;
ALTER TABLE tulajdonsag
  ADD CONSTRAINT tulajdonsagnev_fk FOREIGN KEY (tulajdonsagnev_id) REFERENCES tulajdonsagnev (id) on delete set null;

COMMIT;
