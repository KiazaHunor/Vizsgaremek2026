CREATE TABLE team_kits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    team_id INT NOT NULL UNIQUE,
    image_path VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (team_id) REFERENCES teams(id)
);

INSERT INTO team_kits (team_id, image_path) VALUES
(1, 'hatternelkul/fradi.png'),
(40, 'hatternelkul/ujpest.png'),
(176, 'hatternelkul/paks.png'),
(329, 'hatternelkul/barcika.png'),
(147, 'hatternelkul/dvsc.png'),
(237, 'hatternelkul/dvtk.png'),
(94, 'hatternelkul/eto.png'),
(122, 'hatternelkul/mtk.png'),
(206, 'hatternelkul/nyiregy.png'),
(68, 'hatternelkul/puskas.png'),
(301, 'hatternelkul/varda.png'),
(267, 'hatternelkul/zte.png');