import json, time
from urllib.request import urlopen
import requests as req
from http.server import HTTPServer, BaseHTTPRequestHandler
import http.client
import mimetypes

#extrag din fisier emailul si cuvantul ales
with open('email.txt') as fd:
    text = fd.readline()
    email = text[0]
    word = text[1]
fd.close()

#functii folosite pentru a inregistra log-urile si timpul de primire
def writeToLog(file,response):
    text = json.loads(open(file).read())
    text.append(response)
    open(file,'w').write(json.dumps(text))
    
def writeTime(time,criteria):
    with open('benchmark.txt') as fd:
        fd.write(criteria+" time:"+time)
    fd.close()


def getEmail(email):
    Tstart = time.time()
    url = "https://api.eva.pingutil.com/email?email=" + email
    response = req.get(url)
    #data_json = json.loads(response.read())
    
    Tend = time.time()
    writeToLog("logs.json",response.json())
    writeTime(Tend - Tstart, "Email")
    

def getWord(word):
    Tstart = time.time()
    url = "https://api.dictionaryapi.dev/api/v2/entries/en/" + word
    response = req.get(url)
    #data_json = json.loads(response.read())
    
    Tend = time.time()
    writeToLog("logs.json", response.json())
    writeTime(Tend-Tstart, "Word")
    
    
def getSentiment(word):
    Tstart = time.time()
    url = "https://api.meaningcloud.com/sentiment-2.1"
    payload={
        'key': 'c8904ca237886e24b7dc738b927eb128',
        'txt': word,
        'lang': 'en'
    }
    response = req.post(url,data=payload)
    #data_json = json.loads(response.read())
    
    Tend = time.time()
    writeToLog("logs.json", response.json())
    writeTime(Tend-Tstart, "Sentiment")
    
#clasa de initiere server    
class Server(HTTPServer):
    pass
    
#clasa de performat request-urile    
class RequestHandler(BaseHTTPRequestHandler):
    def get(self):
        Tstart = time.time()
        if self.path.find("metrics"):
            readlogs = json.loads(open("logs.json").read())
            
            emailApiCount = 0
            wordApiCount = 0
            sentimentApiCount = 0
            
            success = 0
            
            for l in readlogs:
                if l['data']['email_address']:
                    emailApiCount +=1
                if l['word']:
                    wordApiCount +=1
                if l['score_tag']:
                    sentimentApiCount +=1
                if l['status'] == "success" or l['status']['msg'] == "OK":
                    success +=1
                    
            self.send_response(200)
            self.end_headers()
            
        elif self.path.find("api"):
            parameters=self.path.split("=")[-1]
            email = getEmail(parameters)
            word = getWord(parameters)
            sentiment = getSentiment(word)
            result = dict()
            result['email'] = email['data']['email_address']
            result['word'] = word['word']
            result['sentiment_subjectivity'] = sentiment['agreement']
            result['sentiment_confidence'] = sentiment['confidence']
            result['sentiment_irony'] = sentiment['irony']
            
            self.send_response(200)
            self.end_headers()
            Tend = time.time()
            writeToLog("results.json")
            writeTime(Tend-Tstart, "Operation")
        
        else:
            self.send_response(200)
            self.end_headers()
            html = open("./index.html").read()
            
            
server = Server(('localhost', 8080), RequestHandler)
server.serve_forever()