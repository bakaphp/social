Kanvas Social
============

[![Latest Stable Version](https://poser.pugx.org/kanvas/social/v)](//packagist.org/social/kanvas/social) [![Total Downloads](https://poser.pugx.org/kanvas/social/downloads)](//packagist.org/social/kanvas/social) [![Latest Unstable Version](https://poser.pugx.org/kanvas/social/v/unstable)](//packagist.org/social/kanvas/social) 
[![Tests](https://github.com/bakaphp/social/workflows/Tests/badge.svg?branch=master)](https://github.com/bakaphp/social/actions?query=workflow%3ATests)

Kanvas Social package , allows you to implement a social layer to any kanvas app.

What do we consider a social layer?
- Follow Entity
- User Interactions
- Comment System
- User Feeds
- Group
- Channels
- Comment Interactions

Indexing Elastic Messages
-------------------------

To create a new index for messages use the following command:

``` bash
php cli/cli.php social indexMessages
```

Erasing the messages index
-------------------------

In case you want you want to erase the messages index, in your terminal, execute the following:

``` bash
 php cli/cli.php social eraseMessages
```

Elastic Configuration
---------------------

Update total fields limit for message index
```
curl -s -XPUT https://{elastichost}/messages/_settings  -H 'Content-Type: application/json' -d '{"index.mapping.total_fields.limit": 100}'
```

Allow all object properties of a specific index to be nested , allowing use to use multi nested level query proper.index.property.index 
Example
```
{{URL}}/{{VERSION}}/messages?q=(chs.slug:d54d8317-89bf-4380-acc3-9941d46cf95a,rlmsg.activities_status.id:2)&format=true
```
```
curl -s -XPUT https://{elastichost}/_template/social_messages  -H 'Content-Type: application/json' -d '{
"index_patterns": [
    "message*"
  ],
  "mappings": {
      "dynamic_templates": [
        {
          "nested": {
            "match_mapping_type": "object",
            "mapping": {
              "type": "nested"
            }
          }
        }
      ]
  }
}'
```




Running Tests:
--------
```bash 
composer test
```
