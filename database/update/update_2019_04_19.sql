ALTER TABLE pos_giftcode ADD COLUMN giftcode_authorize_net varchar(255) AFTER giftcode_payment_type;
ALTER TABLE pos_giftcode ADD COLUMN giftcode_billing_payment_information text AFTER giftcode_authorize_net;