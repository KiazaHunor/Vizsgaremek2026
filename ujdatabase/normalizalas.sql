
DROP table if exists teams;
CREATE TABLE teams (id INT NOT NULL AUTO_INCREMENT , nev varchar(255) NOT NULL , PRIMARY KEY (id)) ENGINE = InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;
INSERT INTO teams(nev) SELECT DISTINCT teams FROM players ORDER BY teams;
ALTER TABLE players ADD teams_id INT NULL AFTER teams;
ALTER TABLE players ADD INDEX(teams_id);
UPDATE players SET teams_id=(SELECT teams.id FROM teams WHERE teams.nev=players.teams);
ALTER TABLE players DROP teams;

ALTER TABLE players drop CONSTRAINT IF EXISTS teams_fk;
ALTER TABLE players
  ADD CONSTRAINT teams_fk FOREIGN KEY (teams_id) REFERENCES teams (id) on delete set null;


DROP table if exists positions;
CREATE TABLE positions (id INT NOT NULL AUTO_INCREMENT , nev varchar(100) NOT NULL , PRIMARY KEY (id)) ENGINE = InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;
INSERT INTO positions(nev) SELECT DISTINCT positions FROM players ORDER BY positions;
ALTER TABLE players ADD positions_id INT NOT NULL AFTER datum;
ALTER TABLE players ADD INDEX(positions_id);
UPDATE players SET positions_id=(SELECT positions.id FROM positions WHERE positions.nev=players.positions);
ALTER TABLE players DROP positions;

DROP table if exists nationalities;
CREATE TABLE nationalities (id INT NOT NULL AUTO_INCREMENT , nev varchar(100) NOT NULL , PRIMARY KEY (id)) ENGINE = InnoDB  DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_hungarian_ci;
INSERT INTO nationalities(nev) SELECT DISTINCT nationalities FROM players ORDER BY nationalities;
ALTER TABLE players ADD nationalities_id INT NOT NULL AFTER nationalities;
ALTER TABLE players ADD INDEX(nationalities_id);
UPDATE players SET nationalities_id=(SELECT nationalities.id FROM nationalities WHERE nationalities.nev=players.nationalities);
ALTER TABLE players DROP nationalities;



ALTER TABLE players drop CONSTRAINT IF EXISTS positions_fk,
                    drop CONSTRAINT IF EXISTS nationalities_fk;

ALTER TABLE players
  ADD CONSTRAINT positions_fk FOREIGN KEY (positions_id) REFERENCES positions (id) on delete set null,
  ADD CONSTRAINT nationalities_fk FOREIGN KEY (nationalities_id) REFERENCES nationalities (id) on delete set null;


