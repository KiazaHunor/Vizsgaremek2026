CREATE TABLE team_kits (
    id INT AUTO_INCREMENT PRIMARY KEY,
    team_id INT NOT NULL UNIQUE,
    image_path VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (team_id) REFERENCES teams(id)
);

INSERT INTO team_kits (team_id, image_path) VALUES
(1, 'hatternelkul/fradi.png'),
(39, 'hatternelkul/ujpest.png'),
(175, 'hatternelkul/paks.png'),
(329, 'hatternelkul/barcika.png'),
(145, 'hatternelkul/dvsc.png'),
(236, 'hatternelkul/dvtk.png'),
(92, 'hatternelkul/eto.png'),
(120, 'hatternelkul/mtk.png'),
(205, 'hatternelkul/nyiregy.png'),
(67, 'hatternelkul/puskas.png'),
(300, 'hatternelkul/varda.png'),
(266, 'hatternelkul/zte.png');