CREATE TABLE IF NOT EXISTS admins (
  id int(11) NOT NULL AUTO_INCREMENT,
  adminFName varchar(60) NOT NULL,
  adminLName varchar(60) NOT NULL,
  adminEmail varchar(60) NOT NULL,
  adminPassword varchar(100) NOT NULL,
  PRIMARY KEY (id),
  UNIQUE (adminEmail)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS designers (
  id int(11) NOT NULL AUTO_INCREMENT,
  designer_Fname varchar(60) NOT NULL,
  designer_Lname varchar(60) NOT NULL,
  designer_pic varchar(1000) NOT NULL,
  designer_bio varchar(500) NOT NULL,
  designer_work varchar(4000) NOT NULL,
  designer_hours int(11) NOT NULL,
  designer_available int(11) NOT NULL,
  designer_price int(11) NOT NULL,
  admin_id int,
  CONSTRAINT FK_designers_admins FOREIGN KEY (admin_id) REFERENCES admins(id),
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS cart (
  id int(11) NOT NULL AUTO_INCREMENT,
  user_session varchar(200) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

CREATE TABLE IF NOT EXISTS cart_product (
  id int(11) NOT NULL AUTO_INCREMENT,
  quantity int(11) NOT NULL,
  cart_id int,
  designer_id int,
  CONSTRAINT FK_cp_cart FOREIGN KEY (cart_id) REFERENCES cart(id),
  CONSTRAINT FK_cp_designers FOREIGN KEY (designer_id) REFERENCES designers(id),
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;