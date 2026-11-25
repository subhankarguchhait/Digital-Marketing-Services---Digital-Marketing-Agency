-- Dream Spark — set the new transparent logo on an EXISTING database.
-- phpMyAdmin → select database dream_spark → SQL tab → paste → Go.
-- (A fresh import of dream_spark.sql already contains these values.)
INSERT INTO settings (setting_key, setting_value) VALUES
  ('logo',         'assets/images/brand/dream-spark-logo-horizontal.png'),
  ('logo_stacked', 'assets/images/brand/dream-spark-logo-web.png'),
  ('favicon',      'assets/images/brand/favicon-192.png')
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value), updated_at = NOW();
