drop SEQUENCE IF EXISTS jsondata_id_seq;
drop TABLE IF EXISTS jsondata;

create sequence jsondata_id_seq;

create table jsondata
(
  id integer not null constraint jsondata_pkey primary key,
  data json not null,
  created_date timestamp(0) default NOW() not null,
  download_amount integer default 0 not null,
  url varchar(100) UNIQUE not null,
  deleted boolean default false not null,
  delete_after_first_access boolean default false not null,
  type varchar(255) not null,
  password varchar(100) default NULL::character varying
);
