#creare request
import requests as req, threading

def Request(email, word):
    url = "localhost:8080/api/email=" + email + "+word=" + word
    response = req.get(url)
    