<?php
namespace Codemitte\Sfdc\Soap\Mapping\Type;

/**
 * StatusCode
 */
class StatusCode extends GenericType
{
    const ALL_OR_NONE_OPERATION_ROLLED_BACK = 'ALL_OR_NONE_OPERATION_ROLLED_BACK';
    const ALREADY_IN_PROCESS = 'ALREADY_IN_PROCESS';
    const ASSIGNEE_TYPE_REQUIRED = 'ASSIGNEE_TYPE_REQUIRED';
    const BAD_CUSTOM_ENTITY_PARENT_DOMAIN = 'BAD_CUSTOM_ENTITY_PARENT_DOMAIN';
    const BCC_NOT_ALLOWED_IF_BCC_COMPLIANCE_ENABLED = 'BCC_NOT_ALLOWED_IF_BCC_COMPLIANCE_ENABLED';
    const CANNOT_CASCADE_PRODUCT_ACTIVE = 'CANNOT_CASCADE_PRODUCT_ACTIVE';
    const CANNOT_CHANGE_FIELD_TYPE_OF_APEX_REFERENCED_FIELD = 'CANNOT_CHANGE_FIELD_TYPE_OF_APEX_REFERENCED_FIELD';
    const CANNOT_CREATE_ANOTHER_MANAGED_PACKAGE = 'CANNOT_CREATE_ANOTHER_MANAGED_PACKAGE';
    const CANNOT_DEACTIVATE_DIVISION = 'CANNOT_DEACTIVATE_DIVISION';
    const CANNOT_DELETE_LAST_DATED_CONVERSION_RATE = 'CANNOT_DELETE_LAST_DATED_CONVERSION_RATE';
    const CANNOT_DELETE_MANAGED_OBJECT = 'CANNOT_DELETE_MANAGED_OBJECT';
    const CANNOT_DISABLE_LAST_ADMIN = 'CANNOT_DISABLE_LAST_ADMIN';
    const CANNOT_ENABLE_IP_RESTRICT_REQUESTS = 'CANNOT_ENABLE_IP_RESTRICT_REQUESTS';
    const CANNOT_INSERT_UPDATE_ACTIVATE_ENTITY = 'CANNOT_INSERT_UPDATE_ACTIVATE_ENTITY';
    const CANNOT_MODIFY_MANAGED_OBJECT = 'CANNOT_MODIFY_MANAGED_OBJECT';
    const CANNOT_RENAME_APEX_REFERENCED_FIELD = 'CANNOT_RENAME_APEX_REFERENCED_FIELD';
    const CANNOT_RENAME_APEX_REFERENCED_OBJECT = 'CANNOT_RENAME_APEX_REFERENCED_OBJECT';
    const CANNOT_REPARENT_RECORD = 'CANNOT_REPARENT_RECORD';
    const CANNOT_UPDATE_CONVERTED_LEAD = 'CANNOT_UPDATE_CONVERTED_LEAD';
    const CANT_DISABLE_CORP_CURRENCY = 'CANT_DISABLE_CORP_CURRENCY';
    const CANT_UNSET_CORP_CURRENCY = 'CANT_UNSET_CORP_CURRENCY';
    const CHILD_SHARE_FAILS_PARENT = 'CHILD_SHARE_FAILS_PARENT';
    const CIRCULAR_DEPENDENCY = 'CIRCULAR_DEPENDENCY';
    const COMMUNITY_NOT_ACCESSIBLE = 'COMMUNITY_NOT_ACCESSIBLE';
    const CUSTOM_CLOB_FIELD_LIMIT_EXCEEDED = 'CUSTOM_CLOB_FIELD_LIMIT_EXCEEDED';
    const CUSTOM_ENTITY_OR_FIELD_LIMIT = 'CUSTOM_ENTITY_OR_FIELD_LIMIT';
    const CUSTOM_FIELD_INDEX_LIMIT_EXCEEDED = 'CUSTOM_FIELD_INDEX_LIMIT_EXCEEDED';
    const CUSTOM_INDEX_EXISTS = 'CUSTOM_INDEX_EXISTS';
    const CUSTOM_LINK_LIMIT_EXCEEDED = 'CUSTOM_LINK_LIMIT_EXCEEDED';
    const CUSTOM_TAB_LIMIT_EXCEEDED = 'CUSTOM_TAB_LIMIT_EXCEEDED';
    const DELETE_FAILED = 'DELETE_FAILED';
    const DELETE_OPERATION_TOO_LARGE = 'DELETE_OPERATION_TOO_LARGE';
    const DELETE_REQUIRED_ON_CASCADE = 'DELETE_REQUIRED_ON_CASCADE';
    const DEPENDENCY_EXISTS = 'DEPENDENCY_EXISTS';
    const DUPLICATE_CASE_SOLUTION = 'DUPLICATE_CASE_SOLUTION';
    const DUPLICATE_COMM_NICKNAME = 'DUPLICATE_COMM_NICKNAME';
    const DUPLICATE_CUSTOM_ENTITY_DEFINITION = 'DUPLICATE_CUSTOM_ENTITY_DEFINITION';
    const DUPLICATE_CUSTOM_TAB_MOTIF = 'DUPLICATE_CUSTOM_TAB_MOTIF';
    const DUPLICATE_DEVELOPER_NAME = 'DUPLICATE_DEVELOPER_NAME';
    const DUPLICATE_EXTERNAL_ID = 'DUPLICATE_EXTERNAL_ID';
    const DUPLICATE_MASTER_LABEL = 'DUPLICATE_MASTER_LABEL';
    const DUPLICATE_SENDER_DISPLAY_NAME = 'DUPLICATE_SENDER_DISPLAY_NAME';
    const DUPLICATE_USERNAME = 'DUPLICATE_USERNAME';
    const DUPLICATE_VALUE = 'DUPLICATE_VALUE';
    const EMAIL_NOT_PROCESSED_DUE_TO_PRIOR_ERROR = 'EMAIL_NOT_PROCESSED_DUE_TO_PRIOR_ERROR';
    const EMPTY_SCONTROL_FILE_NAME = 'EMPTY_SCONTROL_FILE_NAME';
    const ENTITY_FAILED_IFLASTMODIFIED_ON_UPDATE = 'ENTITY_FAILED_IFLASTMODIFIED_ON_UPDATE';
    const ENTITY_IS_ARCHIVED = 'ENTITY_IS_ARCHIVED';
    const ENTITY_IS_DELETED = 'ENTITY_IS_DELETED';
    const ENTITY_IS_LOCKED = 'ENTITY_IS_LOCKED';
    const ERROR_IN_MAILER = 'ERROR_IN_MAILER';
    const FAILED_ACTIVATION = 'FAILED_ACTIVATION';
    const FIELD_CUSTOM_VALIDATION_EXCEPTION = 'FIELD_CUSTOM_VALIDATION_EXCEPTION';
    const FIELD_FILTER_VALIDATION_EXCEPTION = 'FIELD_FILTER_VALIDATION_EXCEPTION';
    const FIELD_INTEGRITY_EXCEPTION = 'FIELD_INTEGRITY_EXCEPTION';
    const FILTERED_LOOKUP_LIMIT_EXCEEDED = 'FILTERED_LOOKUP_LIMIT_EXCEEDED';
    const HTML_FILE_UPLOAD_NOT_ALLOWED = 'HTML_FILE_UPLOAD_NOT_ALLOWED';
    const IMAGE_TOO_LARGE = 'IMAGE_TOO_LARGE';
    const INACTIVE_OWNER_OR_USER = 'INACTIVE_OWNER_OR_USER';
    const INSUFFICIENT_ACCESS_ON_CROSS_REFERENCE_ENTITY = 'INSUFFICIENT_ACCESS_ON_CROSS_REFERENCE_ENTITY';
    const INSUFFICIENT_ACCESS_OR_READONLY = 'INSUFFICIENT_ACCESS_OR_READONLY';
    const INVALID_ACCESS_LEVEL = 'INVALID_ACCESS_LEVEL';
    const INVALID_ARGUMENT_TYPE = 'INVALID_ARGUMENT_TYPE';
    const INVALID_ASSIGNEE_TYPE = 'INVALID_ASSIGNEE_TYPE';
    const INVALID_ASSIGNMENT_RULE = 'INVALID_ASSIGNMENT_RULE';
    const INVALID_BATCH_OPERATION = 'INVALID_BATCH_OPERATION';
    const INVALID_CONTENT_TYPE = 'INVALID_CONTENT_TYPE';
    const INVALID_CREDIT_CARD_INFO = 'INVALID_CREDIT_CARD_INFO';
    const INVALID_CROSS_REFERENCE_KEY = 'INVALID_CROSS_REFERENCE_KEY';
    const INVALID_CROSS_REFERENCE_TYPE_FOR_FIELD = 'INVALID_CROSS_REFERENCE_TYPE_FOR_FIELD';
    const INVALID_CURRENCY_CONV_RATE = 'INVALID_CURRENCY_CONV_RATE';
    const INVALID_CURRENCY_CORP_RATE = 'INVALID_CURRENCY_CORP_RATE';
    const INVALID_CURRENCY_ISO = 'INVALID_CURRENCY_ISO';
    const INVALID_DATA_CATEGORY_GROUP_REFERENCE = 'INVALID_DATA_CATEGORY_GROUP_REFERENCE';
    const INVALID_DATA_URI = 'INVALID_DATA_URI';
    const INVALID_EMAIL_ADDRESS = 'INVALID_EMAIL_ADDRESS';
    const INVALID_EMPTY_KEY_OWNER = 'INVALID_EMPTY_KEY_OWNER';
    const INVALID_FIELD = 'INVALID_FIELD';
    const INVALID_FIELD_FOR_INSERT_UPDATE = 'INVALID_FIELD_FOR_INSERT_UPDATE';
    const INVALID_FIELD_WHEN_USING_TEMPLATE = 'INVALID_FIELD_WHEN_USING_TEMPLATE';
    const INVALID_FILTER_ACTION = 'INVALID_FILTER_ACTION';
    const INVALID_GOOGLE_DOCS_URL = 'INVALID_GOOGLE_DOCS_URL';
    const INVALID_ID_FIELD = 'INVALID_ID_FIELD';
    const INVALID_INET_ADDRESS = 'INVALID_INET_ADDRESS';
    const INVALID_LINEITEM_CLONE_STATE = 'INVALID_LINEITEM_CLONE_STATE';
    const INVALID_MASTER_OR_TRANSLATED_SOLUTION = 'INVALID_MASTER_OR_TRANSLATED_SOLUTION';
    const INVALID_MESSAGE_ID_REFERENCE = 'INVALID_MESSAGE_ID_REFERENCE';
    const INVALID_OPERATION = 'INVALID_OPERATION';
    const INVALID_OPERATOR = 'INVALID_OPERATOR';
    const INVALID_OR_NULL_FOR_RESTRICTED_PICKLIST = 'INVALID_OR_NULL_FOR_RESTRICTED_PICKLIST';
    const INVALID_PACKAGE_VERSION = 'INVALID_PACKAGE_VERSION';
    const INVALID_PARTNER_NETWORK_STATUS = 'INVALID_PARTNER_NETWORK_STATUS';
    const INVALID_PERSON_ACCOUNT_OPERATION = 'INVALID_PERSON_ACCOUNT_OPERATION';
    const INVALID_READ_ONLY_USER_DML = 'INVALID_READ_ONLY_USER_DML';
    const INVALID_SAVE_AS_ACTIVITY_FLAG = 'INVALID_SAVE_AS_ACTIVITY_FLAG';
    const INVALID_SESSION_ID = 'INVALID_SESSION_ID';
    const INVALID_SETUP_OWNER = 'INVALID_SETUP_OWNER';
    const INVALID_STATUS = 'INVALID_STATUS';
    const INVALID_TYPE = 'INVALID_TYPE';
    const INVALID_TYPE_FOR_OPERATION = 'INVALID_TYPE_FOR_OPERATION';
    const INVALID_TYPE_ON_FIELD_IN_RECORD = 'INVALID_TYPE_ON_FIELD_IN_RECORD';
    const IP_RANGE_LIMIT_EXCEEDED = 'IP_RANGE_LIMIT_EXCEEDED';
    const LICENSE_LIMIT_EXCEEDED = 'LICENSE_LIMIT_EXCEEDED';
    const LIGHT_PORTAL_USER_EXCEPTION = 'LIGHT_PORTAL_USER_EXCEPTION';
    const LIMIT_EXCEEDED = 'LIMIT_EXCEEDED';
    const MALFORMED_ID = 'MALFORMED_ID';
    const MANAGER_NOT_DEFINED = 'MANAGER_NOT_DEFINED';
    const MASSMAIL_RETRY_LIMIT_EXCEEDED = 'MASSMAIL_RETRY_LIMIT_EXCEEDED';
    const MASS_MAIL_LIMIT_EXCEEDED = 'MASS_MAIL_LIMIT_EXCEEDED';
    const MAXIMUM_CCEMAILS_EXCEEDED = 'MAXIMUM_CCEMAILS_EXCEEDED';
    const MAXIMUM_DASHBOARD_COMPONENTS_EXCEEDED = 'MAXIMUM_DASHBOARD_COMPONENTS_EXCEEDED';
    const MAXIMUM_HIERARCHY_LEVELS_REACHED = 'MAXIMUM_HIERARCHY_LEVELS_REACHED';
    const MAXIMUM_SIZE_OF_ATTACHMENT = 'MAXIMUM_SIZE_OF_ATTACHMENT';
    const MAXIMUM_SIZE_OF_DOCUMENT = 'MAXIMUM_SIZE_OF_DOCUMENT';
    const MAX_ACTIONS_PER_RULE_EXCEEDED = 'MAX_ACTIONS_PER_RULE_EXCEEDED';
    const MAX_ACTIVE_RULES_EXCEEDED = 'MAX_ACTIVE_RULES_EXCEEDED';
    const MAX_APPROVAL_STEPS_EXCEEDED = 'MAX_APPROVAL_STEPS_EXCEEDED';
    const MAX_FORMULAS_PER_RULE_EXCEEDED = 'MAX_FORMULAS_PER_RULE_EXCEEDED';
    const MAX_RULES_EXCEEDED = 'MAX_RULES_EXCEEDED';
    const MAX_RULE_ENTRIES_EXCEEDED = 'MAX_RULE_ENTRIES_EXCEEDED';
    const MAX_TASK_DESCRIPTION_EXCEEEDED = 'MAX_TASK_DESCRIPTION_EXCEEEDED';
    const MAX_TM_RULES_EXCEEDED = 'MAX_TM_RULES_EXCEEDED';
    const MAX_TM_RULE_ITEMS_EXCEEDED = 'MAX_TM_RULE_ITEMS_EXCEEDED';
    const MERGE_FAILED = 'MERGE_FAILED';
    const MISSING_ARGUMENT = 'MISSING_ARGUMENT';
    const MIXED_DML_OPERATION = 'MIXED_DML_OPERATION';
    const NONUNIQUE_SHIPPING_ADDRESS = 'NONUNIQUE_SHIPPING_ADDRESS';
    const NO_APPLICABLE_PROCESS = 'NO_APPLICABLE_PROCESS';
    const NO_ATTACHMENT_PERMISSION = 'NO_ATTACHMENT_PERMISSION';
    const NO_INACTIVE_DIVISION_MEMBERS = 'NO_INACTIVE_DIVISION_MEMBERS';
    const NO_MASS_MAIL_PERMISSION = 'NO_MASS_MAIL_PERMISSION';
    const NUMBER_OUTSIDE_VALID_RANGE = 'NUMBER_OUTSIDE_VALID_RANGE';
    const NUM_HISTORY_FIELDS_BY_SOBJECT_EXCEEDED = 'NUM_HISTORY_FIELDS_BY_SOBJECT_EXCEEDED';
    const OPTED_OUT_OF_MASS_MAIL = 'OPTED_OUT_OF_MASS_MAIL';
    const OP_WITH_INVALID_USER_TYPE_EXCEPTION = 'OP_WITH_INVALID_USER_TYPE_EXCEPTION';
    const PACKAGE_LICENSE_REQUIRED = 'PACKAGE_LICENSE_REQUIRED';
    const PORTAL_NO_ACCESS = 'PORTAL_NO_ACCESS';
    const PORTAL_USER_ALREADY_EXISTS_FOR_CONTACT = 'PORTAL_USER_ALREADY_EXISTS_FOR_CONTACT';
    const PRIVATE_CONTACT_ON_ASSET = 'PRIVATE_CONTACT_ON_ASSET';
    const RECORD_IN_USE_BY_WORKFLOW = 'RECORD_IN_USE_BY_WORKFLOW';
    const REQUEST_RUNNING_TOO_LONG = 'REQUEST_RUNNING_TOO_LONG';
    const REQUIRED_FEATURE_MISSING = 'REQUIRED_FEATURE_MISSING';
    const REQUIRED_FIELD_MISSING = 'REQUIRED_FIELD_MISSING';
    const SELF_REFERENCE_FROM_TRIGGER = 'SELF_REFERENCE_FROM_TRIGGER';
    const SHARE_NEEDED_FOR_CHILD_OWNER = 'SHARE_NEEDED_FOR_CHILD_OWNER';
    const SINGLE_EMAIL_LIMIT_EXCEEDED = 'SINGLE_EMAIL_LIMIT_EXCEEDED';
    const STANDARD_PRICE_NOT_DEFINED = 'STANDARD_PRICE_NOT_DEFINED';
    const STORAGE_LIMIT_EXCEEDED = 'STORAGE_LIMIT_EXCEEDED';
    const STRING_TOO_LONG = 'STRING_TOO_LONG';
    const TABSET_LIMIT_EXCEEDED = 'TABSET_LIMIT_EXCEEDED';
    const TEMPLATE_NOT_ACTIVE = 'TEMPLATE_NOT_ACTIVE';
    const TERRITORY_REALIGN_IN_PROGRESS = 'TERRITORY_REALIGN_IN_PROGRESS';
    const TEXT_DATA_OUTSIDE_SUPPORTED_CHARSET = 'TEXT_DATA_OUTSIDE_SUPPORTED_CHARSET';
    const TOO_MANY_APEX_REQUESTS = 'TOO_MANY_APEX_REQUESTS';
    const TOO_MANY_ENUM_VALUE = 'TOO_MANY_ENUM_VALUE';
    const TRANSFER_REQUIRES_READ = 'TRANSFER_REQUIRES_READ';
    const UNABLE_TO_LOCK_ROW = 'UNABLE_TO_LOCK_ROW';
    const UNAVAILABLE_RECORDTYPE_EXCEPTION = 'UNAVAILABLE_RECORDTYPE_EXCEPTION';
    const UNDELETE_FAILED = 'UNDELETE_FAILED';
    const UNKNOWN_EXCEPTION = 'UNKNOWN_EXCEPTION';
    const UNSPECIFIED_EMAIL_ADDRESS = 'UNSPECIFIED_EMAIL_ADDRESS';
    const UNSUPPORTED_APEX_TRIGGER_OPERATON = 'UNSUPPORTED_APEX_TRIGGER_OPERATON';
    const UNVERIFIED_SENDER_ADDRESS = 'UNVERIFIED_SENDER_ADDRESS';
    const USER_OWNS_PORTAL_ACCOUNT_EXCEPTION = 'USER_OWNS_PORTAL_ACCOUNT_EXCEPTION';
    const USER_WITH_APEX_SHARES_EXCEPTION = 'USER_WITH_APEX_SHARES_EXCEPTION';
    const WEBLINK_SIZE_LIMIT_EXCEEDED = 'WEBLINK_SIZE_LIMIT_EXCEEDED';
    const WRONG_CONTROLLER_TYPE = 'WRONG_CONTROLLER_TYPE';
    
    static function getName()
    {
        return 'StatusCode';
    }
}
