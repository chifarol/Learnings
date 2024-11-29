# Queues

- Juat like real-life queues, someone can get added to the queue, be removed from the queue prematurely, or be successfully “processed” and then removed. Someone might even hit the front of the queue, not be able to be served correctly, return to the queue for a time, and then be processed again.

- Queues in programming are very similar. Your application adds a “job” to a queue, which is a chunk of code that tells the application how to perform a particular behav‐ ior

- Then some other separate application structure, usually a “queue worker,” takes the responsibility for pulling jobs off of the queue one at a time and performing the appropriate behavior. Queue workers can delete the jobs, return them to the queue with a delay, or mark them as successfully processed.