# About
It might be useful, if you have microservice architecture, and you need access to remote model from BFF or other facade.
# Usage
you need configure access to you remote model
```
'modelName' => [
    'class' => Class\Associated\with\you\model::class,
    'baseUrl' => url to service contain you model,
    'fields' => [
        'fieldName' => ['required' => false],
        ...
    ],
    'findParams' => [
        //Provide query params for find you model
        'fieldName'
        //query will be baseUrl?fieldName={$model->fieldName}
    ],
    'extractor' => [
        // extractor for you data
        // default data extractors will be used when concrete extractors not specified
        'default' => [
            \App\RemoteModel\DataExtractor\ArrayFirstDataExtractor::class,
        ],
        // extractor for list query
        'compare' => [
            \App\RemoteModel\DataExtractor\ArrayFirstDataExtractor::class,
            \App\RemoteModel\DataExtractor\TranslationExtractor::class,
        ],
        // save output extractor
        'save' => [
            \App\RemoteModel\DataExtractor\InnerDataExtractor::class,
        ],
        // extractor for get one query
        'get' => [
            \App\RemoteModel\DataExtractor\InnerDataExtractor::class,
            \App\RemoteModel\DataExtractor\TranslationExtractor::class
        ]
    ],
]
```
default url bind like
* getList `baseUrl`
* getOne `baseUrl/{id}`
* create `baseUrl`
* update `baseUrl/{id}`
* delete `baseUrl/{id}`

you also can replace urls, by create config for it
to replace:
* getList 
```
// define specific find url
findUrl => specific/find/url/%s/model
// define fields for replace
findQuery => ['fieldName']
```
other same
# Features
If you need another extractor, you can implement extractorInterface and add it to configuration.
If it's useful create pr
# Work in progress
delete operation not supported(yet)
# Other
questions, suggestion, improvement, bugfix, feedback on mail glushenok32@gmail.com or issue. 
