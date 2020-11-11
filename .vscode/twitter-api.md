- [Twitter API V1.1](#twitter-api-v11)
  - [Tweets](#tweets)
    - [Standard & Premium search API](#standard--premium-search-api)
    - [Enterprise Search API](#enterprise-search-api)
    - [Post, Retrieve & Engage](#post-retrieve--engage)
- [Twitter API V2](#twitter-api-v2)
  - [User Lookup](#user-lookup)
  - [Tweet Lookup](#tweet-lookup)
  - [Search Tweets: recent search](#search-tweets-recent-search)
  - [Filtered stream](#filtered-stream)
  - [Sampled stream](#sampled-stream)
  - [Hide replies](#hide-replies)


[**Twitter API Homepage**](https://developer.twitter.com/en/docs/twitter-api/getting-started/guide)

---
# Twitter API V1.1

## Tweets

---
### Standard & Premium search API

GET /1.1/search/tweets.json

---
### Enterprise Search API

TODO

---
### Post, Retrieve & Engage

POST statuses/update

POST statuses/destroy/:id

GET statuses/show/:id

GET statuses/oembed

GET statuses/lookup

POST statuses/retweet/:id

POST statuses/unretweet/:id

GET statuses/retweets/:id

GET statuses/retweets_of_me

GET statuses/retweeters/ids

POST favorites/create

POST favorites/destroy

GET favorites/list

POST statuses/update_with_media (deprecated)

---
# Twitter API V2

## User Lookup

GET /2/users
Retrieve multiple users with IDs

GET /2/users/:id
Retrieve a single user with an ID

GET /2/users/by
Retrieve multiple users with usernames

GET /2/users/by/username/:username
Retrieve a single user with a usernames

---
## Tweet Lookup

GET /2/tweets
Retrieve multiple Tweets with a list of IDs

GET /2/tweets/:id
Retrieve a single Tweet with an ID

---
## Search Tweets: recent search

GET /2/tweets/search/recent

---
## Filtered stream

POST /2/tweets/search/stream/rules
Add or delete rules from your stream

GET /2/tweets/search/stream/rules
Retrieve your stream's rules

GET /2/tweets/search/stream
Connect to the stream

---
## Sampled stream

GET /2/tweets/sample/stream

---
## Hide replies

PUT /2/tweets/:id/hidden