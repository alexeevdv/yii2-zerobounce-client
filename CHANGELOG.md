Under development
-----------------

1.6.0 (2019-08-15)
-----------------
- Added methods to send/read and delete .csv files for bulk validation (baskof147)

1.5.0 (2019-06-11)
------------------
- Introduce Exception interface instead of base Exception class (alexeevdv)

1.4.0 (2019-05-03)
------------------
- Add NotAuthorizedException (alexeevdv)

1.3.1 (2019-05-03)
------------------
- Add description and tags to composer.json (alexeevdv)

1.3.0 (2019-05-03)
------------------
- Add `ValidateResponseInterface::isInvalid()` method (alexeevdv)
- Add `ValidateResponseInterface::isCatchAll()` method (alexeevdv)
- Add `ValidateResponseInterface::isSpamTrap()` method (alexeevdv)
- Add `ValidateResponseInterface::isAbuse()` method (alexeevdv)
- Add `ValidateResponseInterface::isDoNotMail()` method (alexeevdv)
- Add `ValidateResponseInterface::isUnknown()` method (alexeevdv)
- Add `ValidateResponseInterface::getMxRecord()` method (alexeevdv)
- Add `ValidateResponseInterface::getFirstName()` method (alexeevdv)
- Add `ValidateResponseInterface::getLastName()` method (alexeevdv)
- Add `ValidateResponseInterface::getGender()` method (alexeevdv)

1.2.0 (2019-05-02)
------------------
- Add `getCredits` method (alexeevdv)

1.1.0 (2019-05-02)
-----------------
- Default timeout is increased to 10 seconds (alexeevdv)
- `isEmailValid` replaced with `validate` (alexeevdv)

1.0.0 (2019-05-02)
-----------------
- `isEmailValid` method added (alexeevdv)
