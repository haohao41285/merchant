ALTER TABLE pos_booking ADD booking_reason TEXT NULL COMMENT 'reason for void ticket, split ticket' AFTER booking_note;